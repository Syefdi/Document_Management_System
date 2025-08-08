import { DataSource } from '@angular/cdk/table';
import { HttpResponse } from '@angular/common/http';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { catchError, finalize } from 'rxjs/operators';
import { ResponseHeader } from '../../core/domain-classes/response-header';
import { DocumentWorkflow } from '@core/domain-classes/document-workflow';
import { WorkflowsResource } from '@core/domain-classes/workflows-resource';
import { DocumentWorkflowService } from '../manage-workflow/document-workflow.service';

export class WorkflowsDataSource implements DataSource<DocumentWorkflow> {

    private workflowsSubject = new BehaviorSubject<DocumentWorkflow[]>([]);
    private responseHeaderSubject = new BehaviorSubject<ResponseHeader>({} as ResponseHeader);
    private loadingSubject = new BehaviorSubject<boolean>(false);

    public loading$ = this.loadingSubject.asObservable();
    private _count: number = 0;


    public get count(): number {
        return this._count;
    }

    public responseHeaderSubject$ = this.responseHeaderSubject.asObservable();

    constructor(private documentWorkflowService: DocumentWorkflowService) { }

    connect(): Observable<DocumentWorkflow[]> {
        return this.workflowsSubject.asObservable();
    }

    disconnect(): void {
        this.workflowsSubject.complete();
        this.loadingSubject.complete();
    }

    loadWorkflows(workflowsResource: WorkflowsResource) {
        this.loadingSubject.next(true);
        this.documentWorkflowService.getAllWorkflowInstances(workflowsResource).pipe(
            catchError(() => of([])),
            finalize(() => this.loadingSubject.next(false))
        )
            .subscribe(
                (resp: HttpResponse<DocumentWorkflow[]>) => {
                    const paginationParam = new ResponseHeader();
                    paginationParam.pageSize = parseInt(resp.headers.get('pageSize'));
                    paginationParam.totalCount = parseInt(resp.headers.get('totalCount'));
                    paginationParam.skip = parseInt(resp.headers.get('skip'));
                    this.responseHeaderSubject.next(paginationParam);
                    const documentWorkflows = [...resp.body];
                    this._count = documentWorkflows.length;
                    this.workflowsSubject.next(documentWorkflows);
                }
            );
    }
}
