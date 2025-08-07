import { HttpClient, HttpParams, HttpResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { DocumentWorkflow } from '@core/domain-classes/document-workflow';
import { MyWorkflow, NextTransition, VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { Workflow } from '@core/domain-classes/workflow';
import { WorkflowsResource } from '@core/domain-classes/workflows-resource';
import { CommonError } from '@core/error-handler/common-error';
import { CommonHttpErrorService } from '@core/error-handler/common-http-error.service';
import { Observable } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class WorkflowService {

  constructor(
    private httpClient: HttpClient,
    private commonHttpErrorService: CommonHttpErrorService) { }

  getWorkflows(): Observable<Workflow[] | CommonError> {
    const url = 'workflow';
    return this.httpClient.get<Workflow[]>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  getWorkflow(id: string): Observable<Workflow | CommonError> {
    const url = `workflow/${id}`;
    return this.httpClient.get<Workflow>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  addWorkflow(workflow: Workflow): Observable<Workflow | CommonError> {
    const url = `workflow`;
    return this.httpClient.post<Workflow>(url, workflow)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  updateWorkflow(workflow: Workflow): Observable<Workflow | CommonError> {
    const url = `workflow/${workflow.id}`;
    return this.httpClient.put<Workflow>(url, workflow)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  deleteWorkflow(id: string): Observable<Workflow | CommonError> {
    const url = `workflow/${id}`;
    return this.httpClient.delete<Workflow>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  getvisualWorkflow(workflowId: string): Observable<VisualWorkflowInstance | CommonError> {
    const url = `workflow/${workflowId}/visualWorkflow`;
    return this.httpClient.get<VisualWorkflowInstance>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  //TODO: move this to document workflow service
  
}
