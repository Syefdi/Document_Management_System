import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule, FormsModule } from '@angular/forms';
import { Router, ActivatedRoute, RouterModule } from '@angular/router';

import { Subject, takeUntil } from 'rxjs';
import { Rack } from '@core/domain-classes/rack';
import { RackService } from '../rack.service';
import { RackStore } from '../rack-store';
import { CommonError } from '@core/error-handler/common-error';
import { ToastrService } from 'ngx-toastr';
import { TranslateModule } from '@ngx-translate/core';
import { FeatherModule } from 'angular-feather';
import { SharedModule } from '@shared/shared.module';

@Component({
  selector: 'app-manage-rack',
  standalone: true,
  imports: [
    FormsModule,
    TranslateModule,
    CommonModule,
    RouterModule,
    ReactiveFormsModule,

    FeatherModule,
    SharedModule
  ],
  templateUrl: './manage-rack.component.html',
  styleUrls: ['./manage-rack.component.scss']
})
export class ManageRackComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();
  
  rackForm: FormGroup;
  isEditMode = false;
  rackId: string | null = null;
  loading = false;

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private rackService: RackService,
    private rackStore: RackStore,
    private toastr: ToastrService
  ) {
    this.initializeForm();
  }

  ngOnInit(): void {
    this.checkRouteParams();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  private initializeForm(): void {
    this.rackForm = this.fb.group({
      name: ['', [Validators.required, Validators.maxLength(100)]],
      description: ['', Validators.maxLength(500)]
    });
  }

  private checkRouteParams(): void {
    this.rackId = this.route.snapshot.paramMap.get('id');
    if (this.rackId) {
      this.isEditMode = true;
      this.loadRack();
    }
  }



  private loadRack(): void {
    if (!this.rackId) return;
    
    this.loading = true;
    this.rackService.getRack(this.rackId)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (result) => {
          if (result && typeof result === 'object' && 'messages' in result) {
            const error = result as CommonError;
            if (error.error && error.error['message']) {
               this.toastr.error(error.error['message']);
            } else if (error.messages && error.messages.length > 0) {
              this.toastr.error(error.messages[0]);
            } else {
              this.toastr.error('An error occurred');
            }
            // Stay on the current form instead of navigating back to list
            // this.router.navigate(['/racks']);
          } else {
            const rack = result as Rack;
            this.rackForm.patchValue({
              name: rack.name,
              description: rack.description
            });
          }
          this.loading = false;
        },
        error: (error) => {
          if (error && error.error && error.error['message']) {
             this.toastr.error(error.error['message']);
          } else if (error && error.messages && error.messages.length > 0) {
            this.toastr.error(error.messages[0]);
          } else {
            this.toastr.error('Failed to load rack');
          }
          this.loading = false;
          this.router.navigate(['/racks']);
        }
      });
  }

  saveRack(): void {
    if (this.rackForm.invalid) {
      this.markFormGroupTouched();
      return;
    }

    this.loading = true;
    const rackData: Rack = {
      ...this.rackForm.value,
      id: this.isEditMode ? this.rackId : undefined
    };

    const operation = this.isEditMode 
      ? this.rackService.updateRack(rackData)
      : this.rackService.addRack(rackData);

    operation
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (result) => {
          if (result && typeof result === 'object' && 'messages' in result) {
            const error = result as CommonError;
            if (error.error && error.error['message']) {
               this.toastr.error(error.error['message']);
            } else if (error.messages && error.messages.length > 0) {
              this.toastr.error(error.messages[0]);
            } else {
              this.toastr.error('An error occurred');
            }
          } else {
            const rack = result as Rack;
            if (this.isEditMode) {
              this.rackStore.updateRack(rack);
              this.toastr.success('Rack updated successfully', 'Success');
            } else {
              this.rackStore.addRack(rack);
              this.toastr.success('Rack created successfully', 'Success');
            }
            // Navigate back to list after successful save
            setTimeout(() => {
              this.router.navigate(['/racks']);
            }, 1500); // Give time for user to see the success message
          }
          this.loading = false;
        },
        error: (error) => {
          if (error && error.error && error.error['message']) {
             this.toastr.error(error.error['message']);
          } else if (error && error.messages && error.messages.length > 0) {
            this.toastr.error(error.messages[0]);
          } else {
            this.toastr.error(`Failed to ${this.isEditMode ? 'update' : 'create'} rack`);
          }
          this.loading = false;
        }
      });
  }

  private markFormGroupTouched(): void {
    Object.keys(this.rackForm.controls).forEach(key => {
      const control = this.rackForm.get(key);
      control?.markAsTouched();
    });
  }

  getErrorMessage(fieldName: string): string {
    const control = this.rackForm.get(fieldName);
    if (control?.hasError('required')) {
      return `${fieldName} is required`;
    }
    if (control?.hasError('maxlength')) {
      return `${fieldName} is too long`;
    }
    if (control?.hasError('min')) {
      return `${fieldName} must be greater than 0`;
    }
    return '';
  }
}