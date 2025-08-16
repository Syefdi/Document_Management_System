import { CommonModule } from '@angular/common';
import { Component, inject, Inject, OnInit } from '@angular/core';
import { ReactiveFormsModule, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { DocumentInfo } from '@core/domain-classes/document-info';
import { DocumentWorkflow } from '@core/domain-classes/document-workflow';
import { Workflow } from '@core/domain-classes/workflow';
import { TranslationService } from '@core/services/translation.service';
import { NgSelectModule } from '@ng-select/ng-select';
import { SharedModule } from '@shared/shared.module';
import { FeatherModule } from 'angular-feather';
import { ToastrService } from 'ngx-toastr';
import { map, Observable, startWith } from 'rxjs';
import { WorkflowService } from 'src/app/workflows/manage-workflow/workflow-service';
import { WorkflowStore } from 'src/app/workflows/manage-workflow/workflow-store';
import { MatAutocompleteModule, MatOption } from '@angular/material/autocomplete';
import { DocumentWorkflowService } from 'src/app/workflows/manage-workflow/document-workflow.service';

@Component({
  selector: 'app-document-workflow-dialog',
  standalone: true,
  imports: [
    MatDialogModule,
    FeatherModule,
    SharedModule,
    ReactiveFormsModule,
    MatButtonModule,
    NgSelectModule,
    CommonModule,
    MatAutocompleteModule
  ],
  templateUrl: './document-workflow-dialog.component.html',
  styleUrl: './document-workflow-dialog.component.scss'
})
export class DocumentWorkflowDialogComponent implements OnInit {
  documentworkflowForm: UntypedFormGroup;
  workflows: Workflow[] = [];
  public workflowStore = inject(WorkflowStore);
  filteredWorkflows: Observable<Workflow[]>;

  constructor(
    private fb: UntypedFormBuilder,
    private toastrService: ToastrService,
    private translationService: TranslationService,
    private documentWorkflowService: DocumentWorkflowService,
    private dialogRef: MatDialogRef<DocumentWorkflowDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { document: DocumentInfo },
  ) { }
  ngOnInit(): void {
    this.createForm();
    this.filteredWorkflows = this.documentworkflowForm.get('workflowName').valueChanges.pipe(
      startWith(''),
      map(value => this._filter(value || '')),
    );
  }

  createForm(): void {
    this.documentworkflowForm = this.fb.group({
      workflowId: ['', [Validators.required]],
      documentId: [this.data.document.id],
      workflowName: [''],
    });
  }

  onWorkflowSelected(workflowName: string): void {
    const workflow = this.workflowStore.activeWorkflows().find(w => w.name.toLowerCase() === workflowName.toLowerCase());
    this.documentworkflowForm.get('workflowId').setValue(workflow.id);
    this.documentworkflowForm.markAllAsTouched();
    this.documentworkflowForm.updateValueAndValidity();
  }

  saveWorkflowInstance(): void {
    if (this.documentworkflowForm.invalid) {
      this.documentworkflowForm.markAllAsTouched();
      return;
    }
    const workflowId = this.documentworkflowForm.get('workflowId').value;

    const documentWorkflow: DocumentWorkflow = {
      workflowId: workflowId,
      documentId: this.data.document.id,
    };
    this.documentWorkflowService.addDocumentWorkflow(documentWorkflow)
      .subscribe(test => {
        this.toastrService.success(
          this.translationService.getValue('WORKFLOW_CREATED_SUCCESSFULLY')
        );
        this.dialogRef.close(documentWorkflow);
      });
  }

  closeDialog() {
    this.dialogRef.close();
  }

  private _filter(value: string): Workflow[] {
    const filterValue = value.toLowerCase();
    if (!filterValue) {
      this.documentworkflowForm.get('workflowId').setValue('');
      this.documentworkflowForm.updateValueAndValidity();
    }
    return this.workflowStore.activeWorkflows().filter(option => option.name.toLowerCase().includes(filterValue));
  }
}
