import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { CommonError } from '@core/error-handler/common-error';
import { CommonHttpErrorService } from '@core/error-handler/common-http-error.service';
import { Observable } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { WorkflowStep } from '@core/domain-classes/workflow-step';

@Injectable({
  providedIn: 'root',
})
export class WorkflowStepService {
  constructor(
    private httpClient: HttpClient,
    private commonHttpErrorService: CommonHttpErrorService
  ) { }

  getWorkflowSteps(): Observable<WorkflowStep[] | CommonError> {
    const url = 'WorkflowStep';
    return this.httpClient
      .get<WorkflowStep[]>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  getWorkflowStep(id: string): Observable<WorkflowStep | CommonError> {
    const url = `WorkflowStep/${id}`;
    return this.httpClient
      .get<WorkflowStep>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }



  addWorkflowStep(steps: WorkflowStep[]): Observable<WorkflowStep[] | CommonError> {
    const url = `workflowStep`;
    return this.httpClient.post<WorkflowStep[]>(url, steps)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  updateWorkflowStep(steps: WorkflowStep[]): Observable<WorkflowStep[] | CommonError> {
   let workflowId = steps[0].workflowId;
    const url = `workflowStep/${workflowId}`;
    return this.httpClient.put<WorkflowStep[]>(url, steps)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }

  deleteWorkflowStep(id: string): Observable<WorkflowStep | CommonError> {
    const url = `WorkflowStep/${id}`;
    return this.httpClient
      .delete<WorkflowStep>(url)
      .pipe(catchError(this.commonHttpErrorService.handleError));
  }
}
