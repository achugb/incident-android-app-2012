package com.spandexman.incident2012;

import android.app.Activity;
import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebSettings.ZoomDensity;

public class MapActivity extends Activity{
	
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    
		WebView wbview = new WebView(this);
		setContentView(wbview);
		wbview.setBackgroundColor(0);
		wbview.getSettings().setBuiltInZoomControls(true);
		wbview.getSettings().setUseWideViewPort(true);
		wbview.getSettings().setDefaultZoom(ZoomDensity.FAR);
		wbview.getSettings().setLoadWithOverviewMode(true);
		wbview.loadUrl("file:///android_asset/map.jpg");
	}
}
