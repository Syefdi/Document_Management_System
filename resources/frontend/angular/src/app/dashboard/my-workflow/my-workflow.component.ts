import { Component, inject, OnInit, ViewChild } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MyWorkflow, NextTransition, VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { VisualWorkflowGraphComponent } from 'src/app/workflows/visual-workflow-graph/visual-workflow-graph.component';
import { BasePreviewComponent } from '@shared/base-preview/base-preview.component';
import { CommonDialogService } from '@core/common-dialog/common-dialog.service';
import { TranslationService } from '@core/services/translation.service';
import { ToastrService } from 'ngx-toastr';
import { OverlayPanel } from '@shared/overlay-panel/overlay-panel.service';
import { TooltipPosition } from '@angular/material/tooltip';
import { BaseComponent } from '../../base.component';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';
import { DocumentWorkflowService } from 'src/app/workflows/manage-workflow/document-workflow.service';
import { WorkflowTransition } from '@core/domain-classes/workflow-transition';
import { DocumentService } from 'src/app/document/document.service';
import { DocumentInfo } from '@core/domain-classes/document-info';
@Component({
  selector: 'app-my-workflow',
  templateUrl: './my-workflow.component.html',
  styleUrl: './my-workflow.component.scss'
})
export class MyWorkflowComponent extends BaseComponent implements OnInit {
  @ViewChild(MatPaginator) paginator: MatPaginator;
  workflowInstances: MyWorkflow[] = [];

  displayedColumns: string[] = ['detail', 'workflowname', 'documentname', 'transition'];
  dataSource: MatTableDataSource<any>;
  isLoading: boolean = true;
  public documentWorkflowService = inject(DocumentWorkflowService);
  private dialog = inject(MatDialog);
  private commonDialogService = inject(CommonDialogService);
  private translationService = inject(TranslationService);
  private toastrService = inject(ToastrService);
  public documentService = inject(DocumentService);
  public overlay = inject(OverlayPanel);
  positionOptions: TooltipPosition[] = ['below', 'above', 'left', 'right'];

  ngOnInit(): void {
    this.dataSource = new MatTableDataSource(this.workflowInstances);
    this.dataSource.paginator = this.paginator;
    this.getWorkflows();
  }

  ngAfterViewInit() {
    this.dataSource.paginator = this.paginator;
  }

  getWorkflows(): void {
    this.sub$.sink = this.documentWorkflowService
      .getMyWorkflow()
      .subscribe({
        next: (data: MyWorkflow[]) => {
          this.workflowInstances = data;
          this.dataSource.data = data;
        },
        error: (error) => { },
      });
  }

  performTransition(transition: WorkflowTransition, workflowInstance: MyWorkflow): void {
    this.commonDialogService
      .deleteConfirmWithCommentDialog(
        `${this.translationService.getValue(
          'ARE_YOU_SURE_YOU_WANT_TO_PROCEED_WITH_THIS_WORKFLOW_TRANSITION'
        )}:: ${transition.name} ?`
      )
      .subscribe((commentFlag) => {
        if (commentFlag.flag) {
          const nextTransition: NextTransition = {
            transitionId: transition.id,
            comment: commentFlag.comment,
            documentId: workflowInstance.documentId,
            documentWorkflowId: workflowInstance.documentWorkflowId,
          };
          this.documentWorkflowService.performNextTransition(nextTransition)
            .subscribe({
              next: () => {
                this.toastrService.success(
                  `${transition.name} ${this.translationService.getValue(
                    'HAS_BEEN_SUCCESSFULLY_COMPLETED'
                  )}`
                );
                this.getWorkflows();
              },
              error: () => { },
            });
        }
      });
  }

  viewVisualWorkflow(workflowInstance: MyWorkflow): void {
    this.documentWorkflowService.getRunningVisualWorkflow(workflowInstance.documentWorkflowId)
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

  onDocumentView(documentId) {
    this.documentService.getDocument(documentId).subscribe((document: DocumentInfo) => {
      const urls = document.url.split('.');
      const extension = urls[1];
      const documentView = {
        documentId: document.id,
        name: document.name,
        //url: document.url,
        extension: extension,
        isVersion: false,
        isFromPublicPreview: false,
        isPreviewDownloadEnabled: false,
        // isFileRequestDocument: false,
      };
      this.overlay.open(BasePreviewComponent, {
        position: 'center',
        origin: 'global',
        panelClass: ['file-preview-overlay-container', 'white-background'],
        data: documentView,
      });
    });
  }
  onPageChanged(event): void {
    const pageIndex = event.pageIndex;
    const pageSize = event.pageSize;
  }
}