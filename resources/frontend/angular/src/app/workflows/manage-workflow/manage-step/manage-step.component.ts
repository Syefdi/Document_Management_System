import { CommonModule } from '@angular/common';
import { Component, effect, inject, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, ReactiveFormsModule, Validators, FormArray } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatStepperModule } from '@angular/material/stepper';
import { RouterModule } from '@angular/router';
import { WorkflowStep } from '@core/domain-classes/workflow-step';
import { TranslateModule } from '@ngx-translate/core';
import { FeatherModule } from 'angular-feather';
import { MatSelectModule } from '@angular/material/select';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { TranslationService } from '@core/services/translation.service';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { SharedModule } from '@shared/shared.module';
import { WorkflowStore } from '../workflow-store';

@Component({
  selector: 'app-manage-step',
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
    MatSelectModule,
    MatCheckboxModule,
    SharedModule,
  ],
  templateUrl: './manage-step.component.html',
  styleUrl: './manage-step.component.scss',
})
export class ManageStepComponent implements OnInit {
  private fb = inject(FormBuilder);
  public workflowStore = inject(WorkflowStore);
  private toastrService = inject(ToastrService);
  private translationService = inject(TranslationService);
  private router = inject(Router);

  stepFormGroup: FormGroup;
  isLoading = false;
  currentStep = 1;
  currentWorkflow = this.workflowStore.currentWorkflow();
  workflowSteps = this.currentWorkflow?.workflowSteps;
  isWorkflowSetup = this.currentWorkflow?.isWorkflowSetup;

  get steps(): FormArray {
    return this.stepFormGroup.get('steps') as FormArray;
  }

  constructor() {
    effect(() => {
      const nextStep = this.workflowStore.currentStep();
      if (nextStep !== this.currentStep) {
        this.currentStep = nextStep;
        if (nextStep === 0) {
          this.onPreviousClick();
        } else {
          this.goToWorkflowTransitions();
        }
      }
    }, { allowSignalWrites: true });
  }

  ngOnInit(): void {
    this.createStepFormGroup();
    if (this.workflowSteps && this.workflowSteps?.length === 0) {
      this.addStep(this.currentWorkflow.isWorkflowSetup, null);
      this.addStep(this.currentWorkflow.isWorkflowSetup, null);
    } else {
      this.workflowSteps.forEach((step: WorkflowStep) => {
        this.addStep(this.currentWorkflow.isWorkflowSetup, step);
      });
    }
  }

  goToWorkflowTransitions() {
    this.router.navigate(['/workflow-settings/manage/manage-transitions']);
  }

  checkUniqueStepName(index: number) {
    const name = this.steps.at(index).get('name').value;
    const isNotUnique = this.steps.controls.some(
      (control, i) => i !== index && control.get('name').value === name
    );

    if (isNotUnique) {
      this.steps.at(index).get('name').setErrors({ notUnique: true });
    } else {
      const errors = this.steps.at(index).get('name').errors;

      if (errors) {
        delete errors['notUnique'];

        if (Object.keys(errors).length === 0) {
          this.steps.at(index).get('name').setErrors(null);
        } else {
          this.steps.at(index).get('name').setErrors(errors);
        }
      }
    }
  }

  createStepFormGroup(): void {
    this.stepFormGroup = this.fb.group({
      steps: this.fb.array([]),
    });
  }

  addStep(isDisabled: boolean, workflowStep?: WorkflowStep): void {
    const stepForm = this.fb.group(
      {
        id: [workflowStep?.id],
        name: [
          { value: workflowStep?.name, disabled: false },
          [Validators.required],
        ],
        workflowId: [workflowStep?.workflowId],
      },
      // { validators: atLeastOneRequiredValidator() }
    );
    this.steps.push(stepForm);
  }

  removeStep(index: number): void {
    this.steps.removeAt(index);
  }

  saveWorkflowSteps(): void {
    if (this.stepFormGroup.invalid) {
      this.stepFormGroup.markAllAsTouched();
      return;
    }
    if (this.steps.value.length < 2) {
      this.toastrService.error(
        this.translationService.getValue('PLEASE_ADD_AT_LEAST_TWO_STEPS_TO_THE_WORKFLOW'),
      );
      return;
    }
    this.isLoading = true;
    const stepsData: WorkflowStep[] = this.steps.value.map((step: any) => ({
      ...step,
      workflowId: this.currentWorkflow.id,
    }));
    if (this.isWorkflowSetup) {
      this.workflowStore.updateWorkflowStep(stepsData);
    } else {
      this.workflowStore.addWorkflowStep(stepsData);
    }
  }

  onPreviousClick(): void {
    this.workflowStore.setCurrentStep(0);
    this.router.navigate(['/workflow-settings/manage', this.currentWorkflow.id]);
  }
}
