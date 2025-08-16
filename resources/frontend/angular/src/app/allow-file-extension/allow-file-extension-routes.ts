import { Routes } from '@angular/router';
import { AuthGuard } from '../core/security/auth.guard';
import { ManageAllowFileExtensionComponent } from './manage-allow-file-extension/manage-allow-file-extension.component';
import { AllowFileExtensionResolver } from './allow-file-extension.resolver';

export const ALLOW_FILE_EXTENSION_ROUTES: Routes = [
  {
    path: ':id',
    component: ManageAllowFileExtensionComponent,
    resolve: { allowFileExtension: AllowFileExtensionResolver },
    canActivate: [AuthGuard],
  },
];
