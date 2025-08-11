import { CommonModule } from '@angular/common';
import { Component, inject, OnInit } from '@angular/core';
import { ActivatedRoute, RouterModule } from '@angular/router';

@Component({
  selector: 'app-error-msg',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './error-msg.component.html',
  styleUrl: './error-msg.component.scss'
})
export class ErrorMsgComponent implements OnInit {
  errorCode: string = '';
  route: ActivatedRoute = inject(ActivatedRoute);
  productionUrl: string = '';

  ngOnInit(): void {
    this.route.queryParams.subscribe((params) => {
      if (params['errorCode']) {
        this.errorCode = params['errorCode'];
        this.productionUrl = params['production_url'] || '';
      }
    });
  }
}
