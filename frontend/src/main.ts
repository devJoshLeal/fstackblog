import { bootstrapApplication } from '@angular/platform-browser';
import { appConfig } from './app/app.config';
import { AppComponent } from './app/app.component';
import { LoginComponent } from './app/components/login/login.component';
const appConfigExtended = {
  ...appConfig,
  providers: [...appConfig.providers],
  declarations: [AppComponent, LoginComponent]
};
bootstrapApplication(AppComponent, appConfigExtended)
  .catch((err) => console.error(err));
