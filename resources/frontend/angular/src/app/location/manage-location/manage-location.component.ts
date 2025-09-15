import { CommonModule } from '@angular/common';
import { Component, inject, OnInit } from '@angular/core';
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
export class ManageLocationComponent extends BaseComponent implements OnInit {
  location: Location;
  locationForm: UntypedFormGroup;
  isEditMode = false;

  private fb = inject(UntypedFormBuilder);
  private router = inject(Router);
  private activeRoute = inject(ActivatedRoute);
  public locationStore = inject(LocationStore);

  constructor() {
    super();
    this.subscribeIsAddUpdate();
  }

  ngOnInit(): void {
    this.createLocationForm();
    this.subscribeIsAddUpdate();
    this.sub$.sink = this.activeRoute.data.subscribe(
      (data: { location: Location }) => {
        if (data.location) {
          this.isEditMode = true;
          this.locationForm.patchValue(data.location);
          this.location = data.location;
        }
      });
  }

  createLocationForm() {
    this.locationForm = this.fb.group({
      id: [''],
      name: ['', [Validators.required]],
      description: [''],
      address: [''],
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