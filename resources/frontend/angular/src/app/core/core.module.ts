import { APP_INITIALIZER, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatDialogModule } from '@angular/material/dialog';
import { MatTooltipModule } from '@angular/material/tooltip';
import { RouterModule } from '@angular/router';
import { LoadingIndicatorModule } from '@shared/loading-indicator/loading-indicator.module';
import { SharedModule } from '@shared/shared.module';
import { FormsModule } from '@angular/forms';
import { CommonDialogService } from './common-dialog/common-dialog.service';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { CommonDialogComponent } from './common-dialog/common-dialog.component';
import { CommonDialogCommentComponent } from './common-dialog-comment/common-dialog-comment.component';
import { initializeApp } from './security/initialize-app-factory';
import { JwtHelperService } from '@auth0/angular-jwt';
import { ToastrService } from 'ngx-toastr';
import { SecurityService } from './security/security.service';
import { LicenseInitializerService } from '@mlglobtech/license-validator-docphp';

@NgModule({
  declarations: [CommonDialogComponent, CommonDialogCommentComponent],
  imports: [
    CommonModule,
    FormsModule,
    MatDialogModule,
    RouterModule,
    SharedModule,
    MatTooltipModule,
    LoadingIndicatorModule,
    MatIconModule,
    MatButtonModule,
  ],
  providers: [
    CommonDialogService,
    LicenseInitializerService,
    JwtHelperService,
    ToastrService,
    {
      provide: APP_INITIALIZER,
      useFactory: initializeApp,
      deps: [LicenseInitializerService, ToastrService, SecurityService, JwtHelperService],
      multi: true,
    },
  ],
})
export class CoreModule { }
