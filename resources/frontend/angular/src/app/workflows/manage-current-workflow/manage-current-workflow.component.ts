import { CommonModule } from '@angular/common';
import { Component, inject, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatDialog } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatStepperModule } from '@angular/material/stepper';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatTooltipModule, TooltipPosition } from '@angular/material/tooltip';
import { RouterModule } from '@angular/router';
import { CommonDialogService } from '@core/common-dialog/common-dialog.service';
import { CommonService } from '@core/services/common.service';
import { TranslationService } from '@core/services/translation.service';
import { TranslateModule } from '@ngx-translate/core';
import { OverlayPanel } from '@shared/overlay-panel/overlay-panel.service';
import { PipesModule } from '@shared/pipes/pipes.module';
import { SharedModule } from '@shared/shared.module';
import { FeatherModule } from 'angular-feather';
import { ToastrService } from 'ngx-toastr';
import { BaseComponent } from 'src/app/base.component';
import { MyWorkflow, NextTransition, VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { WorkflowStatusColorDirective } from '../workflow-status-color.directive';
import { WorkflowTransition } from '@core/domain-classes/workflow-transition';
import { VisualWorkflowGraphComponent } from '../visual-workflow-graph/visual-workflow-graph.component';
import { DocumentWorkflowService } from '../manage-workflow/document-workflow.service';
import { BasePreviewComponent } from '@shared/base-preview/base-preview.component';
import { DocumentInfo } from '@core/domain-classes/document-info';
import { DocumentService } from 'src/app/document/document.service';

@Component({
  selector: 'app-manage-current-workflow',
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
    MatStepperModule,
    MatFormFieldModule,
    MatTableModule,
    MatInputModule,
    WorkflowStatusColorDirective,
    SharedModule,
    PipesModule,
    MatPaginatorModule,
    MatTooltipModule,
  ],
  templateUrl: './manage-current-workflow.component.html',
  styleUrl: './manage-current-workflow.component.scss'
})
export class ManageCurrentWorkflowComponent extends BaseComponent
  implements OnInit {
  workflowInstances: MyWorkflow[] = [];
  displayedColumns: string[] = [
    'transition',
    'updatedAt',
    'workflowname',
    'workflowstatus',
    'workflowInitiatedDate',
    'initiatedUser',
    'documentname',
    'lastTransition',
    'updatedAt',
    'performBy',
  ];
  dataSource: MatTableDataSource<any>;
  footerToDisplayed = ['footer'];
  private dialog = inject(MatDialog);
  public overlay = inject(OverlayPanel);
  public documentWorkflowService = inject(DocumentWorkflowService);
  public commonDialogService = inject(CommonDialogService);
  public translationService = inject(TranslationService);
  public commonService = inject(CommonService);
  public toastrService = inject(ToastrService);
  public documentService=inject(DocumentService);
  positionOptions: TooltipPosition[] = ['below', 'above', 'left', 'right'];

  ngOnInit(): void {
    this.getWorkflows();
  }

  getWorkflows() {
    this.sub$.sink = this.documentWorkflowService
      .getMyWorkflow().subscribe({
        next: (data: MyWorkflow[]) => {
          this.workflowInstances = data;
        },
        error: () => { },
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
  
}
