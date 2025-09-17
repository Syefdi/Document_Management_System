import { CommonModule } from '@angular/common';
import { Component, inject, OnInit, OnDestroy } from '@angular/core';
import { FormsModule, ReactiveFormsModule, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatOptionModule } from '@angular/material/core';
import { MatIconModule } from '@angular/material/icon';
import { MatSelectModule } from '@angular/material/select';

import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { Location } from '@core/domain-classes/location';
import { TranslateModule } from '@ngx-translate/core';
import { SharedModule } from '@shared/shared.module';
import { FeatherModule } from 'angular-feather';
import { BaseComponent } from 'src/app/base.component';
import { LocationStore } from '../location-store';
import { LocationService } from '../location.service';
import { CommonError } from '@core/error-handler/common-error';
import { ToastrService } from 'ngx-toastr';
import { Subject, takeUntil } from 'rxjs';
import { toObservable } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-manage-location',
  standalone: true,
  imports: [FormsModule,
    TranslateModule,
    CommonModule,
    RouterModule,
    MatButtonModule,
    ReactiveFormsModule,
    MatSelectModule,
    MatOptionModule,

    FeatherModule,
    MatIconModule,
    MatCardModule,
    SharedModule,
  ],
  templateUrl: './manage-location.component.html',
  styleUrl: './manage-location.component.scss'
})
export class ManageLocationComponent extends BaseComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();
  
  location: Location;
  locationForm: UntypedFormGroup;
  isEditMode = false;
  locationId: string | null = null;
  loading = false;

  private fb = inject(UntypedFormBuilder);
  private router = inject(Router);
  private activeRoute = inject(ActivatedRoute);
  private locationService = inject(LocationService);
  private toastr = inject(ToastrService);
  public locationStore = inject(LocationStore);

  constructor() {
    super();
    this.initializeForm();
  }

  ngOnInit(): void {
    this.checkRouteParams();
    this.subscribeIsAddUpdate();
  }

  override ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  private initializeForm(): void {
    this.locationForm = this.fb.group({
      id: [''],
      name: ['', [Validators.required]],
      description: [''],
      address: [''],
    });
  }

  private checkRouteParams(): void {
    this.locationId = this.activeRoute.snapshot.paramMap.get('id');
    if (this.locationId) {
      this.isEditMode = true;
      this.loadLocation();
    }
  }

  private loadLocation(): void {
    if (!this.locationId) return;
    
    this.loading = true;
    this.locationService.getLocation(this.locationId)
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
            const location = result as Location;
            this.locationForm.patchValue({
              id: location.id,
              name: location.name,
              description: location.description,
              address: location.address
            });
            this.location = location;
          }
          this.loading = false;
        },
        error: (error) => {
          console.error('Error loading location:', error);
          this.toastr.error('Failed to load location data');
          this.loading = false;
        }
      });
  }

  private markFormGroupTouched(formGroup: UntypedFormGroup) {
    (<any>Object).values(formGroup.controls).forEach((control) => {
      control.markAsTouched();

      if (control.controls) {
        this.markFormGroupTouched(control);
      }
    });
  }

  saveLocation() {
    if (this.locationForm.valid) {
      const location = this.createBuildObject();
      if (this.isEditMode) {
        this.locationStore.addUpdateLocation(location);
      } else {
        this.locationStore.addUpdateLocation(location);
      }
    } else {
      this.markFormGroupTouched(this.locationForm);
    }
  }

  subscribeIsAddUpdate() {
    toObservable(this.locationStore.isAddUpdate).subscribe((flag) => {
      if (flag) {
        this.router.navigate(['/locations']);
      }
      this.locationStore.resetFlag();
    });
  }

  createBuildObject(): Location {
    const location: Location = {
      id: this.locationForm.get('id').value,
      name: this.locationForm.get('name').value,
      description: this.locationForm.get('description').value,
      address: this.locationForm.get('address').value,
    }
    return location;
  }
}