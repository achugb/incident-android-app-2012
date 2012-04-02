package com.spandexman.incident2012;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.webkit.WebView;
import android.widget.Button;

public class UpdatesActivity extends Activity{

	WebView wbview;
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    
	    setContentView(R.layout.updatesview);
	    
	    wbview = (WebView)findViewById(R.id.updates_webview);
	    Button refreshbtn = (Button)findViewById(R.id.updates_refreshbtn);
	    wbview.getSettings().setJavaScriptEnabled(true);
	    wbview.setBackgroundColor(0);
	    wbview.loadUrl("file:///android_asset/updates.html");
	    refreshbtn.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				WebView wbview = (WebView)findViewById(R.id.updates_webview);
				wbview.reload();
			}
		});
	    
    
	    
	}
	
}
