import { CommonModule } from '@angular/common';
import { Component, ContentChild, effect, inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatStepperModule } from '@angular/material/stepper';
import { Router, RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';
import { FeatherModule } from 'angular-feather';
import { ManageTransitionComponent } from '../manage-transition/manage-transition.component';
import { Workflow } from '@core/domain-classes/workflow';
import { SharedModule } from '@shared/shared.module';
import { WorkflowStore } from '../workflow-store';
import { ManageStepComponent } from '../manage-step/manage-step.component';

@Component({
  selector: 'app-create-workflow',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    ReactiveFormsModule,
    TranslateModule,
    MatButtonModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule,
    MatStepperModule,
    FeatherModule,
    CommonModule,
    SharedModule
  ],
  templateUrl: './create-workflow.component.html',
  styleUrl: './create-workflow.component.scss'
})
export class CreateWorkflowComponent implements OnInit {
  private fb = inject(FormBuilder);
  public workflowStore = inject(WorkflowStore);
  private router = inject(Router);
  workFormGroup: FormGroup;
  isLoading = false;
  currentWorkflow = this.workflowStore.currentWorkflow();
  currentStep = 0;
  @ContentChild(ManageStepComponent) manageStepComponent!: ManageStepComponent;
  @ContentChild(ManageTransitionComponent) manageTransitionComponent!: ManageTransitionComponent;

  constructor() {
    effect(() => {
      const nextStep = this.workflowStore.currentStep();
      if (nextStep !== this.currentStep) {
        this.currentStep = nextStep;
        this.goToWorkflowStep();
      }
    }, { allowSignalWrites: true });
  }
  ngOnInit(): void {
    this.createFirstFormGroup();
    this.workFormGroup.patchValue(this.currentWorkflow);
  }

  createFirstFormGroup(): void {
    this.workFormGroup = this.fb.group({
      id: [''],
      name: ['', Validators.required],
      description: ['', Validators.required],
    });
  }

  goToWorkflowStep() {
    this.router.navigate(['/workflow-settings/manage/manage-steps']);
  }

  saveWorkflow(): void {
    if (this.workFormGroup.invalid) {
      this.workFormGroup.markAllAsTouched();
      return;
    }
    const workflow: Workflow = this.createWorkflowBuildObject();
    if (workflow.id) {
      this.workflowStore.updateWorkflow(workflow);
    } else {
      this.workflowStore.addWorkflow(workflow);
    }
  }

  createWorkflowBuildObject(): Workflow {
    return {
      id: this.workFormGroup.get('id')?.value,
      name: this.workFormGroup.get('name')?.value,
      description: this.workFormGroup.get('description')?.value,
      isWorkflowSetup: false,
      workflowSteps: [],
      transitions: [],
    };
  }
}
