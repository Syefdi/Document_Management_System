import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { CommonError } from '@core/error-handler/common-error';
import { RackService } from './rack.service';
import { Rack } from '@core/domain-classes/rack';

@Injectable({
    providedIn: 'root'
  })
export class RackResolver  {
    constructor(private rackService: RackService) { }
    resolve(
        route: ActivatedRouteSnapshot,
        state: RouterStateSnapshot
    ): Observable<Rack | CommonError> {
        const id = route.paramMap.get('id');
        return this.rackService.getRack(id!) as Observable<Rack | CommonError>;
    }
}