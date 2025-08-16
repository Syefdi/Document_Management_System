import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { CommonError } from '@core/error-handler/common-error';
import { CommonHttpErrorService } from '@core/error-handler/common-http-error.service';
import { Observable } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { WorkflowTransition } from '@core/domain-classes/workflow-transition';

@Injectable({
    providedIn: 'root',
})
export class WorkflowTransitionService {
    constructor(
        private httpClient: HttpClient,
        private commonHttpErrorService: CommonHttpErrorService
    ) { }

    getWorkflowTransitions(): Observable<WorkflowTransition[] | CommonError> {
        const url = 'WorkflowTransition';
        return this.httpClient
            .get<WorkflowTransition[]>(url)
            .pipe(catchError(this.commonHttpErrorService.handleError));
    }

    getWorkflowTransition(workflowId: string): Observable<WorkflowTransition | CommonError> {
        const url = `workflow/${workflowId}/transitions`;
        return this.httpClient
            .get<WorkflowTransition>(url)
            .pipe(catchError(this.commonHttpErrorService.handleError));
    }

    addWorkflowTransition(transitions: WorkflowTransition[]): Observable<WorkflowTransition[] | CommonError> {
        const url = `workflowTransition`;
        return this.httpClient
            .post<WorkflowTransition[]>(url, transitions)
            .pipe(catchError(this.commonHttpErrorService.handleError));
    }

    updateWorkflowTransition(transitions: WorkflowTransition[]): Observable<WorkflowTransition[] | CommonError> {
        let workflowId = transitions[0].workflowId;
        const url = `workflowTransitions/${workflowId}`;
        return this.httpClient
            .put<WorkflowTransition[]>(url, transitions)
            .pipe(catchError(this.commonHttpErrorService.handleError));
    }

    deleteWorkflowTransition(id: string): Observable<WorkflowTransition | CommonError> {
        const url = `WorkflowTransition/${id}`;
        return this.httpClient
            .delete<WorkflowTransition>(url)
            .pipe(catchError(this.commonHttpErrorService.handleError));
    }
}
