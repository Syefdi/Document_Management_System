import { Injectable } from '@angular/core';
import { Observable, BehaviorSubject } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { tap, catchError, delay } from 'rxjs/operators';
import { AuthToken, UserAuth } from '../domain-classes/user-auth';
import { CommonHttpErrorService } from '../error-handler/common-http-error.service';
import { CommonError } from '../error-handler/common-error';
import { Router } from '@angular/router';
import { ClonerService } from '@core/services/clone.service';
import { environment } from '@environments/environment';
import { CompanyProfile } from '@core/domain-classes/company-profile';
import { User } from '@core/domain-classes/user';
import { LicenseValidatorService } from '@mlglobtech/license-validator-docphp';

@Injectable({ providedIn: 'root' })
export class SecurityService {
  tokenTime: Date;
  clearTimeOutData: any;
  private securityObject$: BehaviorSubject<User> =
    new BehaviorSubject<User>(null);
  private _companyProfile$: BehaviorSubject<CompanyProfile> =
    new BehaviorSubject<CompanyProfile>(null);
  private _token: AuthToken = null;
  private _claims: string[] = null;

  public get SecurityObject(): Observable<User> {
    return this.securityObject$.asObservable();
  }
  public get companyProfile(): Observable<CompanyProfile> {
    return this._companyProfile$;
  }
  private isRefreshingToken = false;

  public get Token(): AuthToken {
    if (this._token) {
      return this._token;
    }
    this._token = this.licenseValidatorService.getJWtToken();
    return this._token;
  }

  public get Claims(): string[] {
    if (this._claims) {
      return this._claims;
    }
    this._claims = this.Token?.claims;
    return this._claims ?? [];
  }

  constructor(
    private http: HttpClient,
    private clonerService: ClonerService,
    private commonHttpErrorService: CommonHttpErrorService,
    private router: Router,
    private licenseValidatorService: LicenseValidatorService
  ) { }

  isLogin(): boolean {
    const authStr = this.licenseValidatorService.getAuthObject();
    const tokenStr = this.licenseValidatorService.getBearerToken();
    if (authStr && tokenStr) {
      setTimeout(() => {
        this.refreshToken();
      }, 1000);
      return true;
    }
    return false;
  }

  login(entity: any): Observable<UserAuth | CommonError> {
    // Initialize security object
    // this.resetSecurityObject();
    return this.http
      .post<UserAuth>('auth/login', entity)
      .pipe(
        tap((resp) => {
          this.tokenTime = new Date();
          const authUser = this.clonerService.deepClone<UserAuth>(resp);
          this.licenseValidatorService.setTokenUserValue(authUser);
          resp.isAuthenticated = this.licenseValidatorService.getAuthObject() != null;
          // localStorage.setItem('authObj', JSON.stringify(authUser.user));
          // localStorage.setItem('bearerToken', authUser.authorisation.token);
          this.securityObject$.next(authUser.user);
          this.refreshToken();
        })
      )
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  refreshToken() {
    const currentDate: Date = new Date();
    currentDate.setMinutes(currentDate.getMinutes() - environment.tokenExpiredTimeInMin);
    let diffTime;
    if (!this.clearTimeOutData) {
      clearTimeout(this.clearTimeOutData);
    }
    if (!this.tokenTime) {
      diffTime = 60000;
      this.tokenTime = new Date();
    } else {
      diffTime = Math.abs(this.tokenTime.getTime() - currentDate.getTime());
    }

    this.clearTimeOutData = setTimeout(() => {
      clearTimeout(this.clearTimeOutData);
      this.refresh()
        .pipe(delay(1000))
        .subscribe((userAuth: UserAuth | null) => {
          if (!userAuth) {
            return;
          }
          this.tokenTime = new Date();
          const authUser = this.clonerService.deepClone<UserAuth>(userAuth);
          this.licenseValidatorService.setTokenUserValue(authUser);
          this.securityObject$.next(authUser.user);
          this.refreshToken();
        });
    }, diffTime);
  }

  refresh(): Observable<UserAuth | CommonError | null> {
    if (this.isRefreshingToken) {
      return null;
    }
    this.isRefreshingToken = true;
    return this.http
      .post<UserAuth>('auth/refresh', {})
      .pipe(
        tap((resp) => {
          const authUser = this.clonerService.deepClone<UserAuth>(resp);
          this.licenseValidatorService.setTokenUserValue(authUser);
          // localStorage.setItem('authObj', JSON.stringify(authUser.user));
          // localStorage.setItem('bearerToken', authUser.authorisation.token);
          this.securityObject$.next(authUser.user);
          this.isRefreshingToken = false;
        }, () => { }, () => {
          this.isRefreshingToken = false;
        })
      )
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  logout(): void {
    this.resetSecurityObject();
  }

  updateProfile(companyProfile: CompanyProfile) {
    if (companyProfile.logoUrl) {
      companyProfile.logoUrl = `${environment.apiUrl}${companyProfile.logoUrl}`;
    }
    if (companyProfile.bannerUrl) {
      companyProfile.bannerUrl = `${environment.apiUrl}${companyProfile.bannerUrl}`;
    }
    if (companyProfile.smallLogoUrl) {
      companyProfile.smallLogoUrl = `${environment.apiUrl}${companyProfile.smallLogoUrl}`;
    }
    this._companyProfile$.next(companyProfile);
  }

  resetSecurityObject(): void {
    localStorage.removeItem(this.licenseValidatorService.keyValues.authObj);
    localStorage.removeItem(this.licenseValidatorService.keyValues.bearerToken);
    this.securityObject$.next(null);
    this._token = null;
    this._claims = null;
    this.router.navigate(['/login']);
  }

  // This method can be called a couple of different ways
  // *hasClaim="'claimType'"  // Assumes claimValue is true
  // *hasClaim="'claimType:value'"  // Compares claimValue to value
  // *hasClaim="['claimType1','claimType2:value','claimType3']"
  // tslint:disable-next-line: typedef
  hasClaim(claimType: string | string[]): boolean {
    let ret = false;
    // See if an array of values was passed in.
    if (typeof claimType === 'string') {
      ret = this.isClaimValid(claimType);
    } else {
      const claims: string[] = claimType;
      if (claims) {
        // tslint:disable-next-line: prefer-for-of
        for (let index = 0; index < claims.length; index++) {
          ret = this.isClaimValid(claims[index]);
          // If one is successful, then let them in
          if (ret) {
            break;
          }
        }
      }
    }
    // return true;
    return ret;
  }

  private isClaimValid(claimType: string): boolean {
    let ret = false;
    // See if the claim type has a value
    // *hasClaim="'claimType:value'"
    if (claimType.indexOf(':') >= 0) {
      const words: string[] = claimType.split(':');
      claimType = words[0].toLowerCase();
    } else {
      claimType = claimType.toLowerCase();
    }
    // Attempt to find the claim
    ret = this.Claims?.find((c) => c.toLowerCase() == claimType) != null;
    return ret;
  }

  getUserDetail(): User {
    const userJson = this.licenseValidatorService.getAuthObject();
    return userJson;
  }

  setUserDetail(user: User) {
    this.licenseValidatorService.setTokenUserValue({
      user: this.clonerService.deepClone<User>(user)
    });
    // localStorage.setItem('authObj', JSON.stringify(user));
  }
}

