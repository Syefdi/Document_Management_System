import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Rack } from '@core/domain-classes/rack';
import { CommonError } from '@core/error-handler/common-error';
import { CommonHttpErrorService } from '@core/error-handler/common-http-error.service';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class RackService {
  constructor(
    private httpClient: HttpClient,
    private commonHttpErrorService: CommonHttpErrorService
  ) {}

  getRacks(): Observable<Rack[] | CommonError> {
    const url = `racks`;
    return this.httpClient.get<Rack[]>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  getRack(id: string): Observable<Rack | CommonError> {
    const url = `racks/${id}`;
    return this.httpClient.get<Rack>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  addRack(rack: Rack): Observable<Rack | CommonError> {
    const url = `racks`;
    return this.httpClient.post<Rack>(url, rack)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  updateRack(rack: Rack): Observable<Rack | CommonError> {
    const url = `racks/${rack.id}`;
    return this.httpClient.put<Rack>(url, rack)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  deleteRack(id: string): Observable<void | CommonError> {
    const url = `racks/${id}`;
    return this.httpClient.delete<void>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }
}