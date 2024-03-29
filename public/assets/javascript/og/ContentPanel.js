/**
 *  Config options:
 *  	- id
 *  	- title
 *  	- iconCls
 *  	- defaultContent
 *  	- plus all Ext.Panel options
 */
og.ContentPanel = function(config) {
	if (!config) config = {};
	if (!config.listeners) config.listeners = {};
	this.onClose = config.onClose;
	this.contentAutoScroll = typeof config.autoscroll == "undefined" || config.autoscroll;
	Ext.apply(config, {
		autoscroll: false,
		layout: 'contentpanel',
		loaded: false,
		cls: 'og-content-panel', // identifies ContentPanels (see: og.getParentContentPanel)
		items: [{
			xtype: 'panel',
			html: ""
		}],
		listeners: Ext.apply(config.listeners, {
			activate: {
				fn: this.activate,
				scope: this
			},
			deactivate: {
				fn: this.deactivate,
				scope: this
			}
		})
	});
	
	og.ContentPanel.superclass.constructor.call(this, config);
	
	this.history = [];
	this.help = '';
	this.contentLoaded = false;
	
	this.onClose = config.onClose;
	
	if (config.refreshOnWorkspaceChange) {
		og.eventManager.addListener('workspace changed', this.reset, this);
	}
	if (config.refreshOnTagChange) {
		og.eventManager.addListener('tag changed', this.reset, this);
	}
	
	// dirty stuff to allow refreshing a content panel when clicking on its tab
	this.on('render', function() {
		var tab = Ext.get("tabs-panel__" + this.id);
		if (tab) {
			og._activeTab = this.id;
			tab.on('click', function() { 
				og._activeTab = this.id;
			}, this);
			tab.on('dblclick', function() { 
				if (this.id == og._activeTab) {
					this.reset();
				}
			}, this);
		}
		if (this.ownerCt) {
			this.ownerCt.on('remove', function(ct, item) {
				if (item == this) {
					if (typeof this.onClose == 'function') {
						this.onClose();
					}
				}
			}, this);
		}
	}, this);
};

Ext.extend(og.ContentPanel, Ext.Panel, {

	activate: function() {
		this.active = true;
		if (this.getComponent(0).activate) {
			this.getComponent(0).activate();
		}
		if (!this.loaded) {
			if (this.initialContent) {
				this.history.push(this.defaultContent);
				this.load(this.initialContent);
				this.initialContent = false;
			} else {
				this.load(this.defaultContent);
			}
		}
		if(this.help.data){
			Ext.getCmp('help-panel').load(this.help);
		}
	},
	
	deactivate: function() {
		this.active = false;
		if (this.getComponent(0).deactivate) {
			this.getComponent(0).deactivate();
		}
	},
	
	setHelp: function(help){
		this.help = help; 
	},
	
	hasHelp: function(){
		if(this.help != ''){
			return true;
		}
		return false;
	},
	
	setPreventClose: function(prevent) {
		if (!this.preventClose && prevent) {
			// if this panel was closable but now we want to prevent it:
			og.ContentPanel.preventCloseCount++;
			if (og.ContentPanel.preventCloseCount) {
				window.onbeforeunload = function() {return lang("confirm unload page");};
			}
		} else if (this.preventClose && !prevent) {
			// if this panel prevented closing and now becomes closable
			og.ContentPanel.preventCloseCount--;
			if (!og.ContentPanel.preventCloseCount) {
				window.onbeforeunload = null;
			}
		}
		this.preventClose = prevent;
	},
	
	confirmClose: function() {
		if (this.preventClose) {
			if (!confirm(lang("confirm leave panel"))) {
				return false;
			}
			this.setPreventClose(false);
		}
		return !this.preventClose;
	},

	load: function(content, isBack, isReload, isReset) {

		if (!this.confirmClose()) {
			if (isBack) {
				// put content back to the history stack
				this.history.push(content);
			}
			return false;
		}
		if (this.content && this.content.onleave) {
			eval(this.content.onleave);
		}
		if (content.type == 'start') {
			if (this.closable) {
				this.ownerCt.remove(this);
			} else {
				this.reset();
			}
			return false;
		} else if (content.type == 'back') {
			if (typeof content.data == 'number') {
				for (var i=0; i < content.data; i++) {
					this.back();
				}
			} else {
				this.back();
			}
			return false;
		} else if (content.type == 'reload') {
			this.reload();
			return false;
		}
		
		/*if (!og.addLocation) {
			og.activeLocation = "";
			og.addLocation = function(info) {
				var type = info.content.type == 'html' ? 'url' : info.content.type;
				var data = info.content.type == 'html' ? info.content.url : info.content.data;
				if (type == 'url') {
					if (data.indexOf('?')) {
						var newargs = [];
						var args = data.substring(data.indexOf('?') + 1);
						args = args.split("&");
						for (var i=0; i < args.length; i++) {
							var arg = args[i].split("=");
							if (arg[0] == 'active_project' ||
									arg[0] == 'active_tag' ||
									arg[0] == 'ajax' ||
									arg[0] == 'current' ||
									arg[0] == '_dc') continue
							newargs.push(args[i]);
						}
						data = "index.php?" + newargs.join("&");
					}
				}
				var loc = encodeURIComponent(info.ws) + "&" +
					encodeURIComponent(info.tag) + "&" +
					encodeURIComponent(info.tab) + "&" +
					encodeURIComponent(type) + "&" +
					encodeURIComponent(data);
				//alert(loc);
				if (og.activeLocation == loc) return;
				og.activeLocation = loc;
				location.href = "#" + loc;
			};
			setInterval(function() {
				var newLocation = location.href;
				var i = newLocation.indexOf('#');
				if (i >= 0) {
					newLocation = newLocation.substring(i + 1);
					if (newLocation != og.activeLocation) {
						og.activeLocation = newLocation;
						var parts = newLocation.split("&");
						var tagp = Ext.getCmp('tag-panel');
						var wsp = Ext.getCmp('workspace-panel');
						var ws = decodeURIComponent(parts[0]);
						var tag = decodeURIComponent(parts[1]);
						var tab = decodeURIComponent(parts[2]);
						var type = decodeURIComponent(parts[3]);
						var data = decodeURIComponent(parts[4]);
						wsp.select(ws);
						tagp.select(tag);
						var tabp = Ext.getCmp(tab);
						var tabsp = Ext.getCmp('tabs-panel');
						tabsp.setActiveTab(tabp);
						tabp.load({
							type: type,
							data: data
						}, true);
					}
				}
			}, 100);
		}
		var tag = Ext.getCmp('tag-panel'); if (tag) tag=tag.getSelectedTag();
		var ws = Ext.getCmp('workspace-panel'); if (ws) ws=ws.getActiveWorkspace();if(ws)ws=ws.id;
		var tab = Ext.getCmp('tabs-panel'); if (tab) tab=tab.getActiveTab();if(tab)tab=tab.id;
		og.addLocation({
			ws: ws,
			tag: tag,
			tab: tab,
			content: content
		});*/
		
		if (this.content && !isBack && this.content.type != 'url' && !content.replace) {
			var skip = false;
			if (this.content.type == 'html' && (content.type == 'url' || content.type == 'html')) {
				// avoid open twice the same content (open it twice but don't add it to the history stack)
				var url1 = this.content.url;
				var url2 = content.url || content.data;
				if (url1 && url2) {
					url1 = url1.replace(/_dc=[^&]*/g, "");
					url2 = url2.replace(/_dc=[^&]*/g, "");
					if (url1 == url2) {
						skip = true;
					}
				}
			}
			if (!skip) {
				this.history.push(this.content);
			}
		}
		if (typeof content == 'string') {
			content = {
				type: 'html',
				data: content
			}
		}

		this.content = content;
		if (content.makeDefault) {
			this.defaultContent = content;
		}
		if (this.content.type != 'url') {
			var i=0;
			while (this.getComponent(i)) {
				if (this.getComponent(i).doNotRemove) {
					this.getComponent(i).hide();
					i++;
				} else {
					this.remove(this.getComponent(i));
				}
			}
			this.doLayout();
		}
		this.setPreventClose(content.preventClose);
		
		
		if (content.type == 'html') {
			if (this.history.length > 0 && !content.noback) {
				var tbar = [{
					text: lang('back'),
					handler: function() {
						this.back();
					},
					scope: this,
					iconCls: 'ico-back'
				},'-'];
			} else if (this.closable) {
				var tbar = [{
					text: lang('cancel'),
					handler: function() {
						this.ownerCt.remove(this);
					},
					scope: this,
					iconCls: 'ico-back'
				},'-'];
			} else if (content.actions) {
				var tbar = [];
			}
			if (content.actions) {
				for (var i=0; i < content.actions.length; i++) {
					if (content.actions[i].title == '-') {
						tbar.push('-');
					} else {
						tbar.push({
							text: content.actions[i].title,
							handler: function() {
								if (this.url.indexOf('javascript:') == 0) {
									var js = this.url.substring(11);
									eval(js);
								} else {
									if (this.target == '_blank') {
										window.open(this.url);
									} else if (this.target) {
										og.openLink(this.url, {caller: this.target});
									} else {
										og.openLink(this.url);
									}
								}
							},
							scope: content.actions[i],
							iconCls: content.actions[i].name
						});
					}
				}
			}
			if (content.notbar){
				tbar = null;
			}
			var html = content.data
			if (content.inlineScripts && content.inlineScripts.length) {
				var id = Ext.id();
				html += '<span id="' + id + '"></span>';
				Ext.lib.Event.onAvailable(id, function() {
					var start = new Date().getTime();
					for (var inlineScriptContentIterator=0; inlineScriptContentIterator < content.inlineScripts.length; inlineScriptContentIterator++) {
						try {
							if (window.execScript) {
								window.execScript(content.inlineScripts[inlineScriptContentIterator]);
							} else {
								window.eval(content.inlineScripts[inlineScriptContentIterator]);
							}
						} catch (e) {
							og.err(e.message);
						}
					}
					var end = new Date().getTime();
					//og.log("scripits: " + (end - start) + " ms");
					var el = document.getElementById(id);
					if (el) Ext.removeNode(el);
				});
			}
			var p = new og.HtmlPanel({
				html: og.extractScripts(html),
				autoScroll: this.contentAutoScroll,//this.initialConfig.autoScroll,
				tbar: tbar
			});
			this.add(p);
			this.doLayout();
		} else if (content.type == 'url') {
			if (this.active) {
				og.openLink(content.data, {caller: this, preventSwitch: true});
			} else {
				this.loaded = false;
			}
		} else if (content.type == 'panel') {
			if (!content.panel) {
				for (var i=0; this.getComponent(i) && !content.panel; i++) {
					if (this.getComponent(i).getXType() == content.data) {
						// a panel of this type has already been loaded => use it
						content.panel = this.getComponent(i);
					}
				}
				if (!content.panel) {
					// create a new panel of the type
					var config = content.config || {};
					config.xtype = content.data || config.xtype;
					if (Ext.ComponentMgr.isRegistered(config.xtype))
						content.panel = Ext.ComponentMgr.create(config);
					else return false;
				} else if (content.config && typeof content.panel.newConfig == 'function') {
					content.panel.newConfig(content.config);
				}
				//content.panel.load();
			}
			if (isReset) {
				if (content.panel) content.panel.reset();
			} else {//if (!isBack) {//if (isReload) {
				if (!isBack || content.data != 'mails-containerpanel') {
					if (content.panel) content.panel.load();
				}
			}

			this.add(content.panel);
			if (content.panel) content.panel.show();
			this.doLayout();
			og.captureLinks(this.id, this);
		} else {
			var html = "<h1>Error: invalid content</h1>";
			html += "<pre>";
			html += og.debug(content);
			html += "</pre>";
			var p = new Ext.Panel({
				html: html,
				autoScroll: true
			});
			this.add(p);
			this.doLayout();
		}
		if (content.type != 'url') {
			this.loaded = true;
		}
		
		return true;
	},
	
	back: function() {
		var prev = this.history.pop();
		if (!prev) {
			this.load({type: 'start'});
/**/	} else if (prev.type == 'html' && prev.url) {
			this.load({type: 'url', data: prev.url}, true);
/**/	} else { 
			this.load(prev, true, true);
		}
	},
	
	reload: function() {
		if (this.content.type == 'html' && this.content.url) {
			this.load({type:'url',data:this.content.url}, true);
		} else {
			this.load(this.content, true, true);
		}
	},
		
	reset: function() {
		if (!this.confirmClose()) return;
		this.loaded = false;
		if (this.active) {
			this.load(this.defaultContent, true, true, true);
		}
		this.history = [];
	}
});

og.ContentPanel.preventCloseCount = 0;