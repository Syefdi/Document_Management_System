import { inject, } from '@angular/core';
import { ActivatedRouteSnapshot, ResolveFn, RouterStateSnapshot } from '@angular/router';
import { Workflow } from '@core/domain-classes/workflow';
import { of } from 'rxjs';
import { tapResponse } from '@ngrx/operators';
import { CommonError } from '@core/error-handler/common-error';
import { WorkflowStore } from './workflow-store';
import { WorkflowService } from './workflow-service';

export const createWorkFlowResolver: ResolveFn<Workflow | CommonError> = (
  route: ActivatedRouteSnapshot,
  state: RouterStateSnapshot,
) => {
  const store = inject(WorkflowStore);
  const workFlowService = inject(WorkflowService);
  const id = route.paramMap.get('id');
  if (id !== 'add') {
    return workFlowService.getWorkflow(id)
      .pipe(
        tapResponse({
          next: c => store.setCurrentWorkflow(c as Workflow),
          error: e => store.setError(e as CommonError),
        })
      )
  } else {
    store.setCurrentWorkflowAsEmpty();
    return of(store.currentWorkflow());
  }
};
