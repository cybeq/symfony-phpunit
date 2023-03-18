import { Component } from '@angular/core';
import {FormControl, FormGroup, Validators} from "@angular/forms";
import {NbpServiceService} from "./nbp-service.service";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'client';
  currency = "usd"
  startDate:any;
  endDate:any;
  data:any;
  error:any;
  constructor(private nbp: NbpServiceService){}
  submit() {

        this.nbp.getFromApi(this.currency, this.startDate, this.endDate).subscribe((res:any)=>{
          console.log(res)
          if(res.error){
            this.printError(res.error);
            return;
          }

          const data  = res.Rate
          for(let all of data)
            if(all.Diff) all.Diff = JSON.parse(all.Diff)

          console.log('data', data)

          this.data = data;

        })
    }


  printError(error:any){
    this.error = error;
    setTimeout(()=>{this.error=null},1000)
  }

}
