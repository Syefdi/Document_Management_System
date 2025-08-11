import { DataSource } from '@angular/cdk/table';
import { HttpResponse } from '@angular/common/http';
import { ResponseHeader } from '@core/domain-classes/response-header';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { catchError, finalize } from 'rxjs/operators';
import { WorkflowLogService } from './workflow-log.service';
import { WorkflowLog } from '@core/domain-classes/workflow-log';
import { WorkflowLogResource } from '@core/domain-classes/workflow-log-resource';

export class WorkflowLogDataSource implements DataSource<WorkflowLog> {

    private workflowLogSubject = new BehaviorSubject<WorkflowLog[]>([]);
    private responseHeaderSubject = new BehaviorSubject<ResponseHeader>(null);
    private loadingSubject = new BehaviorSubject<boolean>(false);

    public loading$ = this.loadingSubject.asObservable();
    private _count: number = 0;


    public get count(): number {
        return this._count;
    }

    public responseHeaderSubject$ = this.responseHeaderSubject.asObservable();

    constructor(private workflowLogService: WorkflowLogService) { }

    connect(): Observable<WorkflowLog[]> {
        return this.workflowLogSubject.asObservable();
    }

    disconnect(): void {
        this.workflowLogSubject.complete();
        this.loadingSubject.complete();
    }

    loadWorkflowLogs(workflowLogResource: WorkflowLogResource) {
        this.loadingSubject.next(true);
        this.workflowLogService.getWorkflowLogs(workflowLogResource).pipe(
            catchError(() => of([])),
            finalize(() => this.loadingSubject.next(false))
        )
            .subscribe(
                (resp: HttpResponse<WorkflowLog[]>) => {
                    const paginationParam = new ResponseHeader();
                    paginationParam.pageSize = parseInt(resp.headers.get('pageSize'));
                    paginationParam.totalCount = parseInt(resp.headers.get('totalCount'));
                    paginationParam.skip = parseInt(resp.headers.get('skip'));
                    this.responseHeaderSubject.next(paginationParam);
                    const workflowLogs = resp.body ? [...resp.body] : [];
                    this._count = workflowLogs.length;
                    this.workflowLogSubject.next(workflowLogs);
                }
            );
    }
}
