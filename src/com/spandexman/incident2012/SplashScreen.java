package com.spandexman.incident2012;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.widget.ImageView;

public class SplashScreen extends Activity{
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        ImageView imgview = new ImageView(this);
        imgview.setScaleType(ImageView.ScaleType.FIT_CENTER);
        imgview.setImageResource(R.drawable.splash);
        setContentView(imgview);
        
        
        final Intent intent = new Intent().setClass(this, Incident2012Activity.class);
        
        Thread splash = new Thread(){
        	
        	@Override
        	public void run(){
        		try
        		{
        			int waited_for = 0;
        			while(waited_for<1500)
        			{
        				sleep(100);
        				waited_for+=100;
        			}
    			} catch(InterruptedException e){
        				//do nothing
    			} finally {
        				finish();
        				startActivity(intent);
    			}
        	}        	
        };
        splash.start();
    }
}
