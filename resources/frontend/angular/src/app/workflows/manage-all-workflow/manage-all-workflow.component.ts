import { CommonModule } from '@angular/common';
import { AfterViewInit, Component, inject, OnInit, ViewChild } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatStepperModule } from '@angular/material/stepper';
import { MatTableModule } from '@angular/material/table';
import { RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';
import { FeatherModule } from 'angular-feather';
import { CommonDialogService } from '@core/common-dialog/common-dialog.service';
import { TranslationService } from '@core/services/translation.service';
import { MatDialog } from '@angular/material/dialog';
import { VisualWorkflowGraphComponent } from '../visual-workflow-graph/visual-workflow-graph.component';
import { MyWorkflow, NextTransition, VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { OverlayPanel } from '@shared/overlay-panel/overlay-panel.service';
import { MatTooltipModule, TooltipPosition } from '@angular/material/tooltip';
import { SharedModule } from '@shared/shared.module';
import { debounceTime, distinctUntilChanged, merge, Observable, Subject, tap } from 'rxjs';
import { MatPaginator, MatPaginatorModule } from '@angular/material/paginator';
import { MatSort, MatSortModule } from '@angular/material/sort';
import { ResponseHeader } from '@core/domain-classes/document-header';
import { BaseComponent } from 'src/app/base.component';
import { PipesModule } from '@shared/pipes/pipes.module';
import { WorkflowStatusColorDirective } from '../workflow-status-color.directive';
import { CommonService } from '@core/services/common.service';
import { ToastrService } from 'ngx-toastr';
import { MatMenuModule } from '@angular/material/menu';
import { WorkflowTransition } from '@core/domain-classes/workflow-transition';
import { DocumentWorkflow } from '@core/domain-classes/document-workflow';
import { WorkflowsResource } from '@core/domain-classes/workflows-resource';
import { WorkflowsDataSource } from './workflows-datasource';
import { DocumentWorkflowService } from '../manage-workflow/document-workflow.service';
import { WorkflowStatusPipe } from '@shared/pipes/workflow-status.pipe';
import { MatSelectChange, MatSelectModule } from '@angular/material/select';


@Component({
  selector: 'app-manage-all-workflow',
  standalone: true,
  imports: [
    FormsModule,
    TranslateModule,
    CommonModule,
    RouterModule,
    MatButtonModule,
    ReactiveFormsModule,
    FeatherModule,
    MatCardModule,
    MatStepperModule,
    MatFormFieldModule,
    MatSortModule,
    MatPaginatorModule,
    MatTableModule,
    MatInputModule,
    MatSelectModule,
    WorkflowStatusColorDirective,
    SharedModule,
    PipesModule,
    MatTooltipModule,
    MatMenuModule,
    WorkflowStatusPipe,
  ],
  templateUrl: './manage-all-workflow.component.html',
  styleUrl: './manage-all-workflow.component.scss',
})
export class ManageAllWorkflowComponent extends BaseComponent implements OnInit, AfterViewInit {
  dataSource: WorkflowsDataSource;
  isLoadingResults = true;
  workflowsResource: WorkflowsResource;
  loading$: Observable<boolean>;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild(MatSort) sort: MatSort;
  footerToDisplayed = ['footer'];

  workflowInstanceStatuses: { key: string; value: number }[] = [];

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

  displayedColumns: string[] = [
    'transition',
    'createdDate',
    'workflowName',
    'status',
    'createdByName',
    'documentName',
    'lastTransition',
    'modifiedDate',
    'modifiedUserName'
  ];
  filteredColumns: string[] = [
    'transition-search',
    'workflowInitiatedDate-search',
    'workflowname-search',
    'workflowstatus-search',
    'initiatedUser-search',
    'documentname-search',
    'workflowstepname-search',
    'workflowstepstatus-search',
    'performBy-search'

  ]
  private dialog = inject(MatDialog);
  public overlay = inject(OverlayPanel);
  public commonDialogService = inject(CommonDialogService);
  public commonService = inject(CommonService);
  public toastrService = inject(ToastrService);
  public translationService = inject(TranslationService);
  positionOptions: TooltipPosition[] = ['below', 'above', 'left', 'right'];

  constructor(
    private documentWorkflowService: DocumentWorkflowService,
  ) {
    super();
    this.workflowsResource = new WorkflowsResource();
    this.workflowsResource.pageSize = 10;
    this.workflowsResource.orderBy = 'modifiedDate desc';
  }

  ngOnInit(): void {
    this.getWorkflowInstanceStatuses();
    this.dataSource = new WorkflowsDataSource(this.documentWorkflowService);
    this.dataSource.loadWorkflows(this.workflowsResource);
    this.getResourceParameter();

    this.sub$.sink = this.filterObservable$
      .pipe(debounceTime(1000), distinctUntilChanged())
      .subscribe((c) => {
        const strArray: Array<string> = c.split(':');
        if (strArray[0] === 'workflowName') {
          this.workflowsResource.workflowName = strArray[1];
        } else if (strArray[0] === 'docName') {
          this.workflowsResource.documentName = strArray[1];
        }
        this.paginator.pageIndex = 0;
        this.workflowsResource.skip = 0;
        this.dataSource.loadWorkflows(this.workflowsResource);
      });
  }

  getWorkflowInstanceStatuses() {
    this.workflowInstanceStatuses = [
      {
        key: 'Initiated',
        value: 0,
      },
      {
        key: 'InProgress',
        value: 1,
      },
      {
        key: 'Completed',
        value: 2,
      },
      {
        key: 'Cancelled',
        value: 3,
      }
    ];
  }

  ngAfterViewInit() {
    this.sort.sortChange.subscribe(() => (this.paginator.pageIndex = 0));
    merge(this.sort.sortChange, this.paginator.page)
      .pipe(
        tap(() => {
          this.workflowsResource.skip =
            this.paginator.pageIndex * this.paginator.pageSize;
          this.workflowsResource.pageSize = this.paginator.pageSize;
          this.workflowsResource.orderBy =
            this.sort.active + ' ' + this.sort.direction;
          this.dataSource.loadWorkflows(this.workflowsResource);
        })
      )
      .subscribe();
  }

  onWorkflowStatusChange(filterValue: MatSelectChange) {
    this.workflowsResource.status = filterValue.value;
    this.workflowsResource.status = filterValue.value;
    this.dataSource.loadWorkflows(this.workflowsResource);
  }

  onWorkflowsChanges(filterValue: string) {
    if (filterValue) {
      this.workflowsResource.workflowName = filterValue;
    } else {
      this.workflowsResource.workflowName = '';
    }
    this.workflowsResource.skip = 0;
    this.paginator.pageIndex = 0;
    this.dataSource.loadWorkflows(this.workflowsResource);
  }

  onDocumentChange(filterValue: string) {
    if (filterValue) {
      this.workflowsResource.documentName = filterValue;
    } else {
      this.workflowsResource.documentName = '';
    }
    this.workflowsResource.skip = 0;
    this.paginator.pageIndex = 0;
    this.dataSource.loadWorkflows(this.workflowsResource);
  }

  getResourceParameter() {
    this.sub$.sink = this.dataSource.responseHeaderSubject$.subscribe(
      (c: ResponseHeader) => {
        if (c) {
          this.workflowsResource.pageSize = c.pageSize;
          this.workflowsResource.skip = c.skip;
          this.workflowsResource.totalCount = c.totalCount;
        }
      }
    );
  }

  performTransition(transition: WorkflowTransition, workflowInstance: MyWorkflow): void {
    this.commonDialogService
      .deleteConfirmWithCommentDialog(
        `${this.translationService.getValue(
          'ARE_YOU_SURE_YOU_WANT_TO_PROCEED_WITH_THIS_WORKFLOW_TRANSITION'
        )}:: ${transition.name} ?`,
      )
      .subscribe((commentFlag) => {
        if (commentFlag.flag) {
          const nextTransition: NextTransition = {
            transitionId: transition.id,
            comment: commentFlag.comment,
            documentId: workflowInstance.documentId,
            documentWorkflowId: workflowInstance.id,
          }
          this.documentWorkflowService
            .performNextTransition(nextTransition)
            .subscribe({
              next: () => {
                this.toastrService.success(
                  `${transition.name} ${this.translationService.getValue(
                    'HAS_BEEN_SUCCESSFULLY_COMPLETED'
                  )}`
                );
                this.dataSource.loadWorkflows(this.workflowsResource);
              },
              error: () => {
              }
            });
        }
      });
  }

  viewVisualWorkflow(workflowInstance: DocumentWorkflow): void {
    this.documentWorkflowService
      .getRunningVisualWorkflow(workflowInstance.id)
      .subscribe({
        next: (data: VisualWorkflowInstance) => {
          data.documentId = workflowInstance.documentId;
          data.documentName = workflowInstance.documentName;
          this.dialog.open(VisualWorkflowGraphComponent, {
            width: '90vw',
            data: Object.assign({}, data),
          });
        }
      });
  }

  cancelWorkflow(workflowInstance: DocumentWorkflow): void {
    this.commonDialogService
      .deleteConfirmWithCommentDialog(
        `${this.translationService.getValue(
          'ARE_YOU_SURE_YOU_WANT_TO_CANCEL_THIS_WORKFLOW'
        )}:: ${workflowInstance.workflowName} ?`
      ).subscribe((commentFlag) => {
        if (commentFlag.flag) {
          this.documentWorkflowService
            .cancelWOrkflow(workflowInstance.id, commentFlag.comment)
            .subscribe({
              next: () => {
                this.toastrService.success(this.translationService.getValue('WORKFLOW_CANCELLED_SUCCESSFULLY'));
                this.dataSource.loadWorkflows(this.workflowsResource);
              },
            });
        }
      });
  }
}
