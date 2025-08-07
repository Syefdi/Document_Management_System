import { CommonModule } from '@angular/common';
import { Component, inject, OnInit } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { LicenseValidatorService } from '@mlglobtech/license-validator-docphp';

@Component({
  selector: 'app-remove-license-key',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule
  ],
  templateUrl: './remove-license-key.component.html',
  styleUrl: './remove-license-key.component.scss'
})
export class RemoveLicenseKeyComponent implements OnInit {
  activatedForm: FormGroup;
  licenseValidatorService = inject(LicenseValidatorService);

  ngOnInit(): void {
    this.createForm();
  }
  createForm(): void {
    this.activatedForm = new FormGroup({
      purchaseCode: new FormControl('', [Validators.required, Validators.minLength(36)])
    });
  }

  onDeactiveLicense(): void {
    if (this.activatedForm.invalid) {
      this.activatedForm.markAllAsTouched();
      return;
    }
    this.licenseValidatorService.onDeactiveLicense(this.activatedForm.value.purchaseCode);
  }
}
