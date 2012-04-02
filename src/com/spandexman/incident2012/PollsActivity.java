package com.spandexman.incident2012;

import android.app.Activity;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;

public class PollsActivity extends Activity{
	
	WebView wbview;
	
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    setContentView(R.layout.pollsview);
	    
	    wbview = (WebView)findViewById(R.id.polls_webview);
	    Button refreshbtn = (Button)findViewById(R.id.polls_refreshbtn);
	    
	    refreshbtn.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				WebView wbview = (WebView)findViewById(R.id.polls_webview);
				wbview.reload();
			}
		});
	    wbview.getSettings().setJavaScriptEnabled(true);
	    wbview.setBackgroundColor(0);
		wbview.getSettings().setUserAgentString(makeUserAgent());
	    wbview.setWebViewClient(new SelfUrlLoadWebViewClient());
	    wbview.loadUrl("file:///android_asset/polls.html");
	    
	}
	
	private String makeUserAgent() {
		String ua;
		SharedPreferences sp = getSharedPreferences("PollsPreferences", 0);
		SharedPreferences.Editor sped = sp.edit();
		
		ua = sp.getString("UA", "this is not the user agent");
		if(ua == "this is not the user agent")
		{
			//TelephonyManager tm = (TelephonyManager) this.getSystemService(TELEPHONY_SERVICE);
			//ua = String.valueOf(tm.getLine1Number().hashCode());
			//ua=tm.getLine1Number();
			// a random number saved as app-settings identifies the user. 
			ua=String.valueOf(Math.random())+String.valueOf(Math.random())+String.valueOf(Math.random());
			sped.putString("UA", ua);
			sped.commit();
		}		
		return ua;
	}

	private class SelfUrlLoadWebViewClient extends WebViewClient {
	    @Override
	    public boolean shouldOverrideUrlLoading(WebView view, String url) {
	        view.loadUrl(url);
	        return true;
	    }
	}
	
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {	
		if ((keyCode == KeyEvent.KEYCODE_BACK) && wbview.canGoBack()) {
	        wbview.goBack();
	        return true;
	    }
	    return super.onKeyDown(keyCode, event);
	}

}