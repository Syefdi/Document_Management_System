import { Component, OnDestroy } from '@angular/core';
<<<<<<< HEAD
import { SubSink } from 'SubSink';
=======
import { SubSink } from 'subsink';
>>>>>>> 6895172fd2f31385a5c656d4e4aa7daeb185abfc

@Component({
    selector: 'app-base',
    template: ``
})
export class BaseComponent implements OnDestroy {
    sub$: SubSink;
    constructor() {
        this.sub$ = new SubSink();
    }
    ngOnDestroy(): void {
        this.sub$.unsubscribe();
    }
}
