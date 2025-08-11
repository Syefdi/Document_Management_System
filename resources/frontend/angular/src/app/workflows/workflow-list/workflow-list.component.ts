import { Component, inject, OnDestroy, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Workflow } from '@core/domain-classes/workflow';
import { BaseComponent } from 'src/app/base.component';
import { WorkflowStore } from '../manage-workflow/workflow-store';
import { CommonDialogService } from '@core/common-dialog/common-dialog.service';
import { TranslationService } from '@core/services/translation.service';
import { WorkflowService } from '../manage-workflow/workflow-service';
import { VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatStepperModule } from '@angular/material/stepper';
import { MatTableModule } from '@angular/material/table';
import { RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';
import { SharedModule } from '@shared/shared.module';
import { FeatherModule } from 'angular-feather';
import { WorkflowGraphComponent } from '../workflow-graph/workflow-graph.component';

@Component({
  selector: 'app-workflow-list',
  standalone: true,
  imports: [FormsModule,
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
    MatFormFieldModule,
    MatTableModule,
    MatInputModule,],
  templateUrl: './workflow-list.component.html',
  styleUrl: './workflow-list.component.scss'
})
export class WorkflowListComponent extends BaseComponent implements OnInit, OnDestroy {

  workflows: Workflow[] = [];
  displayedColumns: string[] = ['action', 'name', 'description', 'setupStatus'];

  private dialog = inject(MatDialog);
  public workflowStore = inject(WorkflowStore);
  private commonDialogService = inject(CommonDialogService);
  private translationService = inject(TranslationService);
  private workflowService = inject(WorkflowService);

  ngOnInit(): void {
    this.getWorkflows();
  }

  getWorkflows(): void {
    this.workflowStore.loadWorkflows();
  }

  deleteWorkflow(workflow: Workflow): void {
    this.commonDialogService
      .deleteConformationDialog(`${this.translationService.getValue('ARE_YOU_SURE_YOU_WANT_TO_DELETE')} ${workflow.name}`)
      .subscribe((flag: boolean) => {
        if (flag) {
          this.workflowStore.deleteWorkflowById(workflow.id);
        }
      });
  }

  viewVisualWorkflow(workflow: Workflow): void {
    this.workflowService.getvisualWorkflow(workflow.id).subscribe({
      next: (data: VisualWorkflowInstance) => {
        const screenWidth = window.innerWidth;
        const dialogWidth = screenWidth < 768 ? '90vw' : '90vw';
        this.dialog.open(WorkflowGraphComponent, {
          maxWidth: dialogWidth,
          data: { ...data },
        });
      },
      error: (error) => {
        console.error('Error loading workflow:', error);
      }
    });
  }

  // ngOnDestroy(): void {
  //   this.workflowStore.isLoading() === false;
  //   this.workflowStore.setCurrentStep(0);
  //   this.workflowStore.commonError === null;
  // }

}