import { AsyncPipe, NgIf } from '@angular/common';
import { AfterViewInit, Component, Input, OnChanges, OnInit, SimpleChanges, ViewChild } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator, MatPaginatorModule } from '@angular/material/paginator';
import { MatSort, MatSortModule } from '@angular/material/sort';
import { MatTableModule } from '@angular/material/table';
import { ResponseHeader } from '@core/domain-classes/document-header';
import { VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { WorkflowLog } from '@core/domain-classes/workflow-log';
import { WorkflowLogResource } from '@core/domain-classes/workflow-log-resource';
import { TranslateModule } from '@ngx-translate/core';
import { FeatherIconsModule } from '@shared/feather-icons.module';
import { PipesModule } from '@shared/pipes/pipes.module';
import { Observable, Subject, debounceTime, distinctUntilChanged, merge, tap } from 'rxjs';
import { BaseComponent } from 'src/app/base.component';
import { DocumentWorkflowService } from 'src/app/workflows/manage-workflow/document-workflow.service';
import { VisualWorkflowGraphComponent } from 'src/app/workflows/visual-workflow-graph/visual-workflow-graph.component';
import { WorkflowLogDataSource } from 'src/app/workflows/workflow-logs/workflow-log-datasource';
import { WorkflowLogService } from 'src/app/workflows/workflow-logs/workflow-log.service';

@Component({
  selector: 'app-document-workflow-logs',
  standalone: true,
  imports: [
    MatPaginatorModule,
    TranslateModule,
    FormsModule,
    ReactiveFormsModule,
    PipesModule,
    FeatherIconsModule,
    MatTableModule,
    MatSortModule,
    NgIf,
    AsyncPipe
  ],
  templateUrl: './document-workflow-logs.component.html',
  styleUrl: './document-workflow-logs.component.scss'
})
export class DocumentWorkflowLogsComponent extends BaseComponent implements OnChanges, OnInit, AfterViewInit {
  @Input() documentId: string = '';
  @Input() shouldLoad = false;

  dataSource: WorkflowLogDataSource;
  displayedColumns: string[] = [
    'detail',
    'createdDate',
    'transitionName',
    'workflowName',
    'createdByName',
    'fromStepName',
    'comment',
  ];
  displayFilteredColumns: string[] = [
    'detail-search',
    'initiateddate-search',
    'transitionname-search',
    'workflowname-search',
    'initiatedUser-search',
    'workflowsteps-search',
    'comment-search'
  ]
  workflowLogResource: WorkflowLogResource;
  loading$: Observable<boolean>;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild(MatSort) sort: MatSort;
  footerToDisplayed = ['footer'];

  _workflowFilter: string;

  public filterObservable$: Subject<string> = new Subject<string>();
  public get WorkflowFilter(): string {
    return this._workflowFilter;
  }

  public set WorkflowFilter(v: string) {
    this._workflowFilter = v;
    const workflowFilter = `workflowName:${v}`;
    this.filterObservable$.next(workflowFilter);
  }

  constructor(private workflowLogService: WorkflowLogService,
    private documentWorkflowService: DocumentWorkflowService,
    private dialog: MatDialog
  ) {
    super();
    this.workflowLogResource = new WorkflowLogResource();
    this.workflowLogResource.pageSize = 10;
    this.workflowLogResource.orderBy = 'createdDate desc';
    this.workflowLogResource.documentId = this.documentId;
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['shouldLoad'] && this.shouldLoad) {
      this.workflowLogResource.documentId = this.documentId;
      this.dataSource.loadWorkflowLogs(this.workflowLogResource);
    }
  }


  ngOnInit(): void {
    this.dataSource = new WorkflowLogDataSource(this.workflowLogService);
    this.getResourceParameter();
    this.sub$.sink = this.filterObservable$
      .pipe(debounceTime(1000), distinctUntilChanged())
      .subscribe((c) => {
        const strArray: Array<string> = c.split(':');
        if (strArray[0] === 'workflowName') {
          this.workflowLogResource.workflowName = strArray[1];
        } else if (strArray[0] === 'docName') {
          this.workflowLogResource.documentName = strArray[1];
        }
        this.paginator.pageIndex = 0;
        this.workflowLogResource.skip = 0;
        this.dataSource.loadWorkflowLogs(this.workflowLogResource);
      });

  }

  ngAfterViewInit() {
    if (this.sort && this.paginator) {
      this.sort.sortChange.subscribe(() => (this.paginator.pageIndex = 0));

      this.sub$.sink = merge(this.sort.sortChange, this.paginator.page)
        .pipe(
          tap(() => {
            this.workflowLogResource.skip =
              this.paginator.pageIndex * this.paginator.pageSize;
            this.workflowLogResource.pageSize = this.paginator.pageSize;
            this.workflowLogResource.orderBy =
              this.sort.active + ' ' + this.sort.direction;
            this.dataSource.loadWorkflowLogs(this.workflowLogResource);
          })
        )
        .subscribe();
    }
  }

  onWorkflowsChanges(filterValue: string) {
    if (filterValue) {
      this.workflowLogResource.workflowName = filterValue;
    } else {
      this.workflowLogResource.workflowName = '';
    }
    this.workflowLogResource.skip = 0;
    this.paginator.pageIndex = 0;
    this.dataSource.loadWorkflowLogs(this.workflowLogResource);
  }

  onDocumentChange(filterValue: string) {
    if (filterValue) {
      this.workflowLogResource.documentName = filterValue;
    } else {
      this.workflowLogResource.documentName = '';
    }
    this.workflowLogResource.skip = 0;
    this.paginator.pageIndex = 0;
    this.dataSource.loadWorkflowLogs(this.workflowLogResource);
  }

  getResourceParameter() {
    this.sub$.sink = this.dataSource.responseHeaderSubject$.subscribe(
      (c: ResponseHeader) => {
        if (c) {
          this.workflowLogResource.pageSize = c.pageSize;
          this.workflowLogResource.skip = c.skip;
          this.workflowLogResource.totalCount = c.totalCount;
        }
      }
    );
  }

  viewVisualWorkflow(workflowLog: WorkflowLog): void {
    this.documentWorkflowService.getRunningVisualWorkflow(workflowLog.documentWorkflowId).subscribe({
      next: (data: VisualWorkflowInstance) => {
        data.documentId = workflowLog.documentId;
        data.documentName = workflowLog.documentName;
        this.dialog.open(VisualWorkflowGraphComponent, {
          width: '90vw',
          data: { ...data },
        });
      },
      error: (error) => {
        console.error('Error loading workflow:', error);
      }
    });
  }
}
