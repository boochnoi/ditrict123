	<div class="container my_content">
    	<div id="loader-wrapper">
            <div id="loader"></div>
    
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
   			
        </div>
    <!--<div  id="awr_fullscreen_loading" style="height:100%;	background-color: rgb(28, 29, 34);    position: absolute;    top: 0;    right: 0;    left: -20px;    z-index: 9999999;    background: rgb(28, 29, 34);   padding: 10px;">
        <div class="awr-loding-gif-cnt" >
			<div class="awr-loading-css">
			 <div class="rect1"></div>
			 <div class="rect2"></div>
			 <div class="rect3"></div>
			 <div class="rect4"></div>
			 <div class="rect5"></div>
		   </div>
		</div>
    </div>-->
    
    <?php
		
		$menu_html='';
    	$our_menu=apply_filters( 'pw_report_wcreport_page_fetch_menu', $visible_menu );
		update_option("pw_report_menus",json_encode($our_menu));

		//print_r($our_menu);
		
		$basic_menu='';
		$tax_field_reports='';
		$more_reports='';
		$cross_menu='';
		$other_menu='';		
		
		
		foreach($our_menu as $roots){
			foreach($roots['childs'] as $childs){
				
				if(defined("__PW_PERMISSION_ADD_ON__"))
				{
					if(!$this->get_menu_capability($childs['id']))
						continue;
				}
				
				if($childs['id']=='dashboard' || $childs['id']=='all_orders')
				{
					$basic_menu.='<a class="menu__link" href="'.$childs['link'].'" id="'.$childs['id'].'"><i class="fa '.$childs['icon'].'"></i>'.$childs['label'].'</a>';
				}
				else if($roots['parent']=='tax_field_reports')
				{
					$tax_field_reports.='<a class="menu__link" href="'.$childs['link'].'" id="'.$childs['id'].'"><i class="fa '.$childs['icon'].'"></i>'.$childs['label'].'</a>';
				}
				else if($roots['parent']=='more_reports')
				{
					$more_reports.='<a class="menu__link" href="'.$childs['link'].'" id="'.$childs['id'].'"><i class="fa '.$childs['icon'].'"></i>'.$childs['label'].'</a>';
				}else if($roots['parent']=='cross_tab')
				{
					$cross_menu.='<a class="menu__link" href="'.$childs['link'].'" id="'.$childs['id'].'"><i class="fa '.$childs['icon'].'"></i>'.$childs['label'].'</a>';
				}else if(!isset($childs['submenu_id']))
				{
					$other_menu.='<a class="menu__link" href="'.$childs['link'].'" id="'.$childs['id'].'"><i class="fa '.$childs['icon'].'"></i>'.$childs['label'].'</a>';
				}
				
			}

		}
	?>
    
	<div class="awr-allmenu-cnt" style="visibility:hidden">
		<div class="awr-allmenu-close"><i class="fa fa-times"></i></div>
		<div class="row">
        	
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-check"></i><?php echo __('Basics',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<?php echo $basic_menu; ?>
				</div>
			</div>
            
            <?php
            	if($tax_field_reports!='')
				{
			?>
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-random"></i><?php echo __('All Order with Taxonomies & Fields',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<?php echo $tax_field_reports; ?>
				</div>
			</div>
            <?php
				}
			?>
            
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-files-o"></i><?php echo __('More Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<?php echo $more_reports; ?>
				</div>
			</div>
            
            <?php
            	if($cross_menu!='')
				{
			?>
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-random"></i><?php echo __('CrossTab',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<?php echo $cross_menu; ?>
				</div>
			</div>
            <?php
				}
			?>
            
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-check"></i><?php echo __('Other',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<?php echo $other_menu; ?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 awr-allmenu-footer">
				<h3><?php echo __('WOOCommerce Advance Reporting System',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></h3>
				<span>Powered By <a href="http://codecanyon.net/user/proword/portfolio">Proword</a></span>
			</div>
		</div><!--row -->
	</div>
    
    
    <?php
		$menu_html='';
		$included_menus='';
		if ($this->dashboard($this->pw_plugin_status)){
			$included_menus='';
		}else{
			$included_menus=array("dashboard","active_plugin");	
			$no_dashboard_menu=array(
							"label" => __('Activate Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "active_plugin",
							"link" => "admin.php?page=wcx_wcreport_plugin_active_report&parent=active_plugin",
							"icon" => "fa-check",
						);			
			
			array_push($visible_menu[0]['childs'],$no_dashboard_menu);			
		}
			
		$our_menu=apply_filters( 'pw_report_wcreport_page_fetch_menu', $visible_menu );
		
		foreach($our_menu as $roots){
			$menu_html.= '<ul data-menu="'.$roots['parent'].'" class="menu__level">';
			foreach($roots['childs'] as $childs){
				
				
				if(defined("__PW_PERMISSION_ADD_ON__"))
				{
					if(!$this->get_menu_capability($childs['id']))
						continue;
				}
				
				if(is_array($included_menus) && !in_array($childs['id'],$included_menus))
					continue;
				
				$submenu_id='';
				if(isset($childs['submenu_id']))
				{
					$submenu_id='data-submenu="'.$childs['id'].'"';
				}else{
					$submenu_id='id="'.$childs['id'].'"';
				}
				
				$all_menu_class="";
				if($childs['id']=='all_menu')
				{
					$all_menu_class="awr-allmenu";
				}
				
				$menu_html.= '<li class="menu__item"><a class="menu__link '.$all_menu_class.'" href="'.$childs['link'].'" '.$submenu_id.'><i class="fa '.$childs['icon'].'"></i>'.$childs['label'].'</a></li>';
			}
			$menu_html.= '</ul>';
		}
		
	?>
    
    <div class="awr-action awr-action--open"></div><!--monile-btn -->
	<nav id="ml-menu" class="awr-menu"  style="visibility:hidden">
		<img class="awr-menu-logo" src="<?php echo __PW_REPORT_WCREPORT_URL__; ?>/assets/images/logo.png" />
		<div class="awr-toggle-menu"></div>
		<div class="menu__wrap">
				
				<?php
                	echo $menu_html;
				?>
				
			</div>
	</nav>
	
    <!-- Main container -->
    
        <div class="awr-content" style="visibility:hidden">
            <?php
				
				if(defined("__PW_PERMISSION_ADD_ON__"))
				{
					
					if(isset($_REQUEST['parent']) && isset($_REQUEST['smenu']))
					{
						$enable_parent_menu=$this->get_menu_capability($_REQUEST['parent']);
						$enable_sub_menu=$this->get_menu_capability($_REQUEST['smenu']);
						
						if($this->get_menu_capability($_REQUEST['parent']) && $this->get_menu_capability($_REQUEST['smenu']))
						{
							if ($this->dashboard($this->pw_plugin_status) || $page=='plugin_active.php' || $page=="dashboard_report.php"){
								include($page);
							}else{
								$page='plugin_active.php';	
								include($page);
							}
						}else{
							echo '
								<div class="wrap">
									<div class="row">
										<div class="col-xs-12">
											<div class="awr-addons-cnt awr-addones-deactive">
												<div class="awr-descicon"><i class="fa fa-times"></i></div>
												<div class="awr-desc-content">	
													<h3 class="awr-addones-title" style="color:#333;border-bottom:1px solid #ccc;padding-bottom:5px">Access Denied!</h3>
													<div class="awr-addnoes-desc">You have no permisson !!</div>
													<a class="awr-addons-btn" href="http://proword.net/request/" target="_blank" style="background: #eee;"><i class="fa fa-paper-plane"></i>Send Your Request</a>
												</div>
												<div class="awr-clearboth"></div>
											</div>
										</div><!--col-xs-12 -->
									</div><!--row -->
								</div><!--wrap -->';
						}
						
					}elseif(isset($_REQUEST['parent']) && !isset($_REQUEST['smenu'])){

						if($this->get_menu_capability($_REQUEST['parent']))
						{
							if ($this->dashboard($this->pw_plugin_status) || $page=='plugin_active.php' || $page=="dashboard_report.php"){
								include($page);
							}else{
								$page='plugin_active.php';	
								include($page);
							}
						}else{
							echo '
								<div class="wrap">
									<div class="row">
										<div class="col-xs-12">
											<div class="awr-addons-cnt awr-addones-deactive">
												<div class="awr-descicon"><i class="fa fa-times"></i></div>
												<div class="awr-desc-content">	
													<h3 class="awr-addones-title" style="color:#333;border-bottom:1px solid #ccc;padding-bottom:5px">Access Denied!</h3>
													<div class="awr-addnoes-desc">You have no permisson !!</div>
													<a class="awr-addons-btn" href="http://proword.net/request/" target="_blank" style="background: #eee;"><i class="fa fa-paper-plane"></i>Send Your Request</a>
												</div>
												<div class="awr-clearboth"></div>
											</div>
										</div><!--col-xs-12 -->
									</div><!--row -->
								</div><!--wrap -->';
						}
					}
				
				}else{
					if ($this->dashboard($this->pw_plugin_status) || $page=='plugin_active.php' || $page=="dashboard_report.php"){
							include($page);
					}else{
						$page='plugin_active.php';	
						include($page);
					}
				}	
				
				
				//echo $parent;
			
				
			?>
            
            <!-- Ajax loaded content here -->
        </div>
    </div>