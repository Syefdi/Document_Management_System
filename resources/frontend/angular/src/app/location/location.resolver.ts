import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { CommonError } from '../core/error-handler/common-error';
import { LocationService } from './location.service';
import { Location } from '@core/domain-classes/location';

@Injectable({
    providedIn: 'root'
  })
export class LocationResolver  {
    constructor(private locationService: LocationService) { }
    resolve(
        route: ActivatedRouteSnapshot,
        state: RouterStateSnapshot
    ): Observable<Location | CommonError> {
        const id = route.paramMap.get('id');
        return this.locationService.getLocation(id) as Observable<Location | CommonError>;
    }
}