import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { CommonError } from '../core/error-handler/common-error';
import { CommonHttpErrorService } from '../core/error-handler/common-http-error.service';
import { Observable } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { Location } from '@core/domain-classes/location';

@Injectable({
  providedIn: 'root',
})
export class LocationService {
  constructor(
    private httpClient: HttpClient,
    private commonHttpErrorService: CommonHttpErrorService,
  ) {}

  getLocations(): Observable<Location[] | CommonError> {
    const url = 'locations';
    return this.httpClient.get<Location[]>(url).pipe(
      catchError(this.commonHttpErrorService.handleError)
    );
  }

  getLocation(id: string): Observable<Location | CommonError> {
    const url = `locations/${id}`;
    return this.httpClient
      .get<Location>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  addLocation(
    location: Location
  ): Observable<Location | CommonError> {
    const url = `locations`;
    return this.httpClient
      .post<Location>(url, location)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  updateLocation(
    location: Location
  ): Observable<Location | CommonError> {
    const url = `locations/${location.id}`;
    return this.httpClient
      .put<Location>(url, location)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  deleteLocation(
    id: string
  ): Observable<Location | CommonError> {
    const url = `locations/${id}`;
    return this.httpClient
      .delete<Location>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }
}