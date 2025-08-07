import { HttpClient, HttpParams, HttpResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { WorkflowLog } from '@core/domain-classes/workflow-log';
import { WorkflowLogResource } from '@core/domain-classes/workflow-log-resource';
import { CommonError } from '@core/error-handler/common-error';
import { CommonHttpErrorService } from '@core/error-handler/common-http-error.service';
import { Observable } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
    providedIn: 'root'
})
export class WorkflowLogService {

    constructor(
        private httpClient: HttpClient,
        private commonHttpErrorService: CommonHttpErrorService) { }

    getWorkflowLogs(resource: WorkflowLogResource): Observable<HttpResponse<WorkflowLog[]> | CommonError> {
        const url = 'workflow-logs';
        const customParams = new HttpParams()
            .set('fields', resource.fields)
            .set('orderBy', resource.orderBy)
            .set('pageSize', resource.pageSize.toString())
            .set('skip', resource.skip.toString())
            .set('searchQuery', resource.searchQuery)
            .set('documentName', resource.documentName)
            .set('workflowName', resource.workflowName)
            .set('status', resource.status)
            .set('documentId', resource.documentId ? resource.documentId : '');

        return this.httpClient.get<WorkflowLog[]>(url, {
            params: customParams,
            observe: 'response'
        }).pipe(catchError(this.commonHttpErrorService.handleError));
    }
}
