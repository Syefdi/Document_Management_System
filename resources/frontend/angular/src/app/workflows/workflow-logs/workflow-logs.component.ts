import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { WorkflowLogResource } from '@core/domain-classes/workflow-log-resource';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { FeatherModule } from 'angular-feather';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { MatStepperModule } from '@angular/material/stepper';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatTableModule } from '@angular/material/table';
import { MatInputModule } from '@angular/material/input';
import { debounceTime, distinctUntilChanged, merge, Observable, Subject, tap } from 'rxjs';
import { MatPaginator, MatPaginatorModule } from '@angular/material/paginator';
import { MatSort, MatSortModule } from '@angular/material/sort';
import { BaseComponent } from 'src/app/base.component';
import { ResponseHeader } from '@core/domain-classes/document-header';
import { PipesModule } from '@shared/pipes/pipes.module';
import { WorkflowLogDataSource } from './workflow-log-datasource';
import { WorkflowLogService } from './workflow-log.service';
import { SharedModule } from '@shared/shared.module';
import { NgSelectModule } from '@ng-select/ng-select';
import { WorkflowLog } from '@core/domain-classes/workflow-log';
import { VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { MatDialog } from '@angular/material/dialog';
import { VisualWorkflowGraphComponent } from '../visual-workflow-graph/visual-workflow-graph.component';
import { DocumentWorkflowService } from '../manage-workflow/document-workflow.service';

@Component({
  selector: 'app-workflow-logs',
  standalone: true,
  imports: [
    FormsModule,
    TranslateModule,
    CommonModule,
    RouterModule,
    MatButtonModule,
    ReactiveFormsModule,
    FeatherModule,
    MatIconModule,
    MatCardModule,
    SharedModule,
    MatStepperModule,
    NgSelectModule,
    MatFormFieldModule,
    MatPaginatorModule,
    PipesModule,
    MatTableModule,
    MatInputModule,
    MatSortModule
  ],
  templateUrl: './workflow-logs.component.html',
  styleUrl: './workflow-logs.component.scss'
})
export class WorkflowLogsComponent extends BaseComponent implements OnInit, AfterViewInit {
  dataSource: WorkflowLogDataSource;
  displayedColumns: string[] = [
    'detail',
    'createdDate',
    'transitionName',
    'workflowName',
    'documentName',
    'createdByName',
    'fromStepName',
    'comment',
  ];
  displayFilteredColumns: string[] = [
    'detail-search',
    'initiateddate-search',
    'transitionname-search',
    'workflowname-search',
    'documentname-search',
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
  _docNameFilter: string

  public filterObservable$: Subject<string> = new Subject<string>();
  public get WorkflowFilter(): string {
    return this._workflowFilter;
  }

  public set WorkflowFilter(v: string) {
    this._workflowFilter = v;
    const workflowFilter = `workflowName:${v}`;
    this.filterObservable$.next(workflowFilter);
  }

  public get DocNameFilter(): string {
    return this._docNameFilter;
  }

  public set DocNameFilter(v: string) {
    this._docNameFilter = v;
    const docNameFilter = `docName:${v}`;
    this.filterObservable$.next(docNameFilter);
  }

  constructor(private workflowLogService: WorkflowLogService,
    private documentWorkflowService: DocumentWorkflowService,
    private dialog: MatDialog
  ) {
    super();
    this.workflowLogResource = new WorkflowLogResource();
    this.workflowLogResource.pageSize = 10;
    this.workflowLogResource.orderBy = 'createdDate desc';
  }

  ngOnInit(): void {
    this.dataSource = new WorkflowLogDataSource(this.workflowLogService);
    this.dataSource.loadWorkflowLogs(this.workflowLogResource);
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
