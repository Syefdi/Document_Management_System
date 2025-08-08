import { CommonModule } from '@angular/common';
import { AfterViewInit, Component, effect, inject, OnInit, ViewChild } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { Router, RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';
import { FilterWorkflowStepPipe } from './filter-workflow-step.pipe';
import { SharedModule } from '@shared/shared.module';
import { MatSelectModule } from '@angular/material/select';
import { MatInputModule } from '@angular/material/input';
import { MatIconModule } from '@angular/material/icon';
import { FeatherModule } from 'angular-feather';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatButtonModule } from '@angular/material/button';
import { fromToStepValidator } from './from-to-step-validate';
import { WorkflowTransition } from '@core/domain-classes/workflow-transition';
import { WorkflowStore } from '../workflow-store';
import { ClonerService } from '@core/services/clone.service';
import { NgxGraphModule, Node } from '@swimlane/ngx-graph';
import { curveMonotoneX } from 'd3-shape';
import { atLeastOneRequiredValidator } from './at-least-one-required-validator';

@Component({
  selector: 'app-manage-transition',
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
    MatSelectModule,
    FeatherModule,
    SharedModule,
    NgxGraphModule,
    FilterWorkflowStepPipe,
    MatCheckboxModule
  ],
  templateUrl: './manage-transition.component.html',
  styleUrl: './manage-transition.component.scss'
})
export class ManageTransitionComponent implements OnInit, AfterViewInit {
  private fb = inject(FormBuilder);
  public workflowStore = inject(WorkflowStore);
  private clonerService = inject(ClonerService);
  private router = inject(Router);

  transitionFormGroup: FormGroup;
  isLoading = false;
  currentStep = 2;
  nodes: Node[] = [];
  links: any[] = [];
  curve = curveMonotoneX;

  currentWorkflow = this.workflowStore.currentWorkflow();
  workflowSteps = this.currentWorkflow?.workflowSteps;
  workflowTransitions = this.currentWorkflow?.transitions;
  @ViewChild('graph') graph: any;

  constructor() {
    effect(() => {
      const nextStep = this.workflowStore.currentStep();
      if (nextStep !== this.currentStep) {
        this.currentStep = nextStep;
        if (nextStep === 1) {
          this.onPreviousClick();
        } else {
          this.goToWorkflow();
        }
      }
    }, { allowSignalWrites: true });
  }

  get transitions(): FormArray {
    return this.transitionFormGroup.get('transitions') as FormArray;
  }

  goToWorkflow() {
    this.router.navigate(['/workflow-settings']);
  }

  ngOnInit(): void {
    this.nodes = this.workflowSteps.map((step) => ({
      id: step.id,
      label: step.name,
      data: step,
    }));

    this.transitionFormGroup = this.fb.group({
      transitions: this.fb.array([]),
    });

    if (this.workflowTransitions && this.workflowTransitions?.length === 0) {
      this.initialTransition();
    } else {
      for (let i = 0; i < this.workflowTransitions.length; i++) {
        this.addTransition(false, this.workflowTransitions[i]);
      }
      // this.workflowTransitions.forEach((transition) => this.addTransition(transition));
      this.addAllLink();
    }
  }
  ngAfterViewInit() {
    setTimeout(() => {
      if (this.graph) {
        this.graph?.zoomToFit(); // Auto zooms to fit view
      }
    });
  }

  checkUniqueTransitionName(index: number) {
    const transitionName = this.transitions.at(index).get('name').value;
    const isNotUnique = this.transitions.controls.some(
      (control, i) => i !== index && control.get('name').value === transitionName
    );

    if (isNotUnique) {
      this.transitions.at(index).get('name').setErrors({ notUnique: true });
    } else {
      const errors = this.transitions.at(index).get('name').errors;

      if (errors) {
        delete errors['notUnique'];

        if (Object.keys(errors).length === 0) {
          this.transitions.at(index).get('name').setErrors(null);
        } else {
          this.transitions.at(index).get('name').setErrors(errors);
        }
      }
    }
  }

  filterSteps(index: number): void {
    this.removeFormItem(index);
    this.addAllLink();
  }

  removeFormItem(index: number): void {
    if (index == this.transitions.length - 1) {
      return;
    }
    const length = this.transitions.length;
    for (let i = index + 1; i < length; i++) {
      this.transitions.removeAt(index + 1);
    }

  }

  addAllLink() {
    const filerArray = this.transitions.getRawValue().filter((transition) => transition.fromStepId && transition.toStepId && transition.name);
    if (filerArray.length > 0) {
      this.links = this.clonerService.deepClone(filerArray.map((transition) => {
        return { source: transition.fromStepId, target: transition.toStepId, label: transition.name }
      }));
    }
  }

  initialTransition(): void {
    this.transitions.push(
      this.fb.group({
        id: [null],
        name: ['', Validators.required],
        fromStepId: ['', Validators.required],
        toStepId: ['', Validators.required],
        isFirstTransaction: [true],
        workflowId: [null],
        color: ['inprogress'],
        roleIds: [[]],
        userIds: [[]],
        orderNo: [0]
      }, { validators: [fromToStepValidator(), atLeastOneRequiredValidator()] }),
    );
  }
  onTransitionChange($event) {
    this.addAllLink();
  }

  addTransition(isFromUI: boolean, transition?: WorkflowTransition): void {
    if (!isFromUI || this.transitions.valid) {
      this.transitions.push(
        this.fb.group({
          id: [transition?.id || null],
          name: [transition?.name || '', Validators.required],
          fromStepId: [{ value: transition?.fromStepId || '', disabled: transition?.id ? true : false }, [Validators.required]],
          toStepId: [{ value: transition?.toStepId || '', disabled: transition?.id ? true : false }, [Validators.required]],
          isFirstTransaction: [transition?.isFirstTransaction || false],
          workflowId: [this.currentWorkflow?.id || null,],
          color: [transition?.color || 'inprogress'],
          orderNo: [transition?.orderNo || 0],
          roleIds: [transition?.workflowTransitionRoles.map((role) => role.roleId) || []],
          userIds: [transition?.workflowTransitionUsers.map((user) => user.userId) || []],
        }, { validators: [fromToStepValidator(), atLeastOneRequiredValidator()] })
      );
    } else {
      this.transitionFormGroup.markAllAsTouched();
    }
  }

  removeTransition(index: number): void {
    this.removeFormItem(index);
    this.transitions.removeAt(index);
    if (this.transitions.length === 0) {
      this.initialTransition();
    }
    this.addAllLink();
  }

  saveTransition(): void {
    if (this.transitionFormGroup.invalid) {
      this.transitionFormGroup.markAllAsTouched();
      return;
    }

    this.isLoading = true;
    let transitionsData: WorkflowTransition[] = this.transitions.getRawValue().map((transition: any) => ({
      ...transition,
      workflowId: this.currentWorkflow.id,
    }));

    for (let index = 0; index < transitionsData.length; index++) {
      transitionsData[index].orderNo = index;
    }

    if (transitionsData[0].id) {
      transitionsData[0].isFirstTransaction = true;
      this.workflowStore.updateWorkflowTransition(transitionsData);
    } else {
      this.workflowStore.addWorkflowTransition(transitionsData);
    }
  }

  onPreviousClick() {
    this.workflowStore.setCurrentStep(1);
    this.router.navigate(['/workflow-settings/manage/manage-steps']);
  }
}

