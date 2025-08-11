import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { SecurityService } from '@core/security/security.service';
import { environment } from '@environments/environment';
import { LicenseValidatorService } from '@mlglobtech/license-validator-docphp';

@Component({
  selector: 'app-activate-license',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
  ],
  templateUrl: './activate-license.component.html',
  styleUrl: './activate-license.component.scss'
})
export class ActivateLicenseComponent implements OnInit {
  logoUrl?: string;
  securityService = inject(SecurityService);
  licenseValidatorService = inject(LicenseValidatorService);
  activatedForm: FormGroup;

  ngOnInit(): void {
    this.createForm();
    this.getCompanyProfile();
  }
  createForm(): void {
    this.activatedForm = new FormGroup({
      purchaseCode: new FormControl('', [Validators.required, Validators.minLength(36)])
    });
  }

  getCompanyProfile(): void {
    this.securityService.companyProfile.subscribe((c) => {
      if (c) {
        this.logoUrl = c.logoUrl;
      }
    });
  }

  onActivateLicense(): void {
    if (this.activatedForm.invalid) {
      this.activatedForm.markAllAsTouched();
      return;
    }
    this.licenseValidatorService.onActivateLicense(this.activatedForm.value.purchaseCode);
  }

}
