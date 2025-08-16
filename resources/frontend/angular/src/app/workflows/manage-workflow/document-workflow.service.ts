import { HttpClient, HttpParams, HttpResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { DocumentWorkflow } from '@core/domain-classes/document-workflow';
import { MyWorkflow, NextTransition, VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { WorkflowsResource } from '@core/domain-classes/workflows-resource';
import { CommonError } from '@core/error-handler/common-error';
import { CommonHttpErrorService } from '@core/error-handler/common-http-error.service';
import { Observable } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class DocumentWorkflowService {

  constructor(
    private httpClient: HttpClient,
    private commonHttpErrorService: CommonHttpErrorService) { }
 

  //TODO: move this to document workflow service
  addDocumentWorkflow(workflow: DocumentWorkflow): Observable<DocumentWorkflow | CommonError> {
    const url = `documentWorkflow`;
    return this.httpClient.post<DocumentWorkflow>(url, workflow)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  getMyWorkflow(): Observable<MyWorkflow[] | CommonError> {
    const url = `my-workflow`;
    return this.httpClient.get<MyWorkflow[]>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  getRunningVisualWorkflow(documentWorkflowId: string): Observable<VisualWorkflowInstance | CommonError> {
    const url = `documentWorkflow/${documentWorkflowId}/visualWorkflow`;
    return this.httpClient.get<VisualWorkflowInstance>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  performNextTransition(nextTransition: NextTransition): Observable<void | CommonError> {
    const url = `documentWorkflow/performNextTransition`;
    return this.httpClient.post<void>(url, nextTransition)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  getAllWorkflowInstances(resource: WorkflowsResource): Observable<HttpResponse<DocumentWorkflow[]> | CommonError> {
    const url = 'documentWorkflow';
    const customParams = new HttpParams()
      .set('fields', resource.fields)
      .set('orderBy', resource.orderBy)
      .set('pageSize', resource.pageSize.toString())
      .set('skip', resource.skip.toString())
      .set('searchQuery', resource.searchQuery)
      .set('documentName', resource.documentName ?? '')
      .set('workflowName', resource.workflowName ?? '')
      .set('status', resource.status ?? '');

    return this.httpClient.get<DocumentWorkflow[]>(url, {
      params: customParams,
      observe: 'response'
    })
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  cancelWOrkflow(id, comment): Observable<void | CommonError> {
    const url = `documentWorkflow/${id}/cancel`;
    return this.httpClient.post<void>(url, {
      comment: comment
    })
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }
}
