package com.spandexman.incident2012;

import android.app.Activity;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;

public class ScheduleActivity extends Activity{
	
	WebView wbview;
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    
    	setContentView(R.layout.scheduleview);
	    
	    wbview = (WebView)findViewById(R.id.schedule_webview);
	    Button refreshbtn = (Button)findViewById(R.id.schedule_refreshbtn);
	    
	    wbview.getSettings().setJavaScriptEnabled(true);
	    wbview.setBackgroundColor(0);
	    wbview.loadUrl("file:///android_asset/schedule.html");
	    
	    refreshbtn.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				WebView wbview = (WebView)findViewById(R.id.schedule_webview);
				wbview.reload();
			}
		});
	}
}
