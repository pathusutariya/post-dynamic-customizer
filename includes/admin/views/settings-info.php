<div class="wrap about-wrap pdc-wrap">
	
	<h1><?php _e("Welcome to Post Dynamic Customizer",'pdc'); ?> <?php echo $version; ?></h1>
	<div class="about-text"><?php printf(__("Thank you for updating! PDC %s is bigger and better than ever before. We hope you like it.", 'pdc'), $version); ?></div>
	
	<h2 class="nav-tab-wrapper">
		<?php foreach( $tabs as $tab_slug => $tab_title ): ?>
			<a class="nav-tab<?php if( $active == $tab_slug ): ?> nav-tab-active<?php endif; ?>" href="<?php echo admin_url("edit.php?post_type=pdc-field-group&page=pdc-settings-info&tab={$tab_slug}"); ?>"><?php echo $tab_title; ?></a>
		<?php endforeach; ?>
	</h2>
	
<?php if( $active == 'new' ): ?>
	
	<div class="feature-section">
		<h2><?php _e("A Smoother Experience", 'pdc'); ?> </h2>
		<div class="pdc-three-col">
			<div>
				<p><img src="https://assets.advancedcustomfields.com/info/5.0.0/select2.png" /></p>
				<h3><?php _e("Improved Usability", 'pdc'); ?></h3>
				<p><?php _e("Including the popular Select2 library has improved both usability and speed across a number of field types including post object, page link, taxonomy and select.", 'pdc'); ?></p>
			</div>
			<div>
				<p><img src="https://assets.advancedcustomfields.com/info/5.0.0/design.png" /></p>
				<h3><?php _e("Improved Design", 'pdc'); ?></h3>
				<p><?php _e("Many fields have undergone a visual refresh to make PDC look better than ever! Noticeable changes are seen on the gallery, relationship and oEmbed (new) fields!", 'pdc'); ?></p>
			</div>
			<div>
				<p><img src="https://assets.advancedcustomfields.com/info/5.0.0/sub-fields.png" /></p>
				<h3><?php _e("Improved Data", 'pdc'); ?></h3>
				<p><?php _e("Redesigning the data architecture has allowed sub fields to live independently from their parents. This allows you to drag and drop fields in and out of parent fields!", 'pdc'); ?></p>
			</div>
		</div>
	</div>
	
	<hr />
	
	<div class="feature-section">
		<h2><?php _e("Goodbye Add-ons. Hello PRO", 'pdc'); ?> 👋</h2>
		<div class="pdc-three-col">
			<div>
				<h3><?php _e("Introducing PDC PRO", 'pdc'); ?></h3>
				<p><?php _e("We're changing the way premium functionality is delivered in an exciting way!", 'pdc'); ?></p>
				<p><?php printf(__('All 4 premium add-ons have been combined into a new <a href="%s">Pro version of PDC</a>. With both personal and developer licenses available, premium functionality is more affordable and accessible than ever before!', 'pdc'), esc_url('https://www.advancedcustomfields.com/pro')); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Powerful Features", 'pdc'); ?></h3>
				<p><?php _e("PDC PRO contains powerful features such as repeatable data, flexible content layouts, a beautiful gallery field and the ability to create extra admin options pages!", 'pdc'); ?></p>
				<p><?php printf(__('Read more about <a href="%s">PDC PRO features</a>.', 'pdc'), esc_url('https://www.advancedcustomfields.com/pro')); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Easy Upgrading", 'pdc'); ?></h3>
				<p><?php _e('Upgrading to PDC PRO is easy. Simply purchase a license online and download the plugin!', 'pdc'); ?></p>
				<p><?php printf(__('We also wrote an <a href="%s">upgrade guide</a> to answer any questions, but if you do have one, please contact our support team via the <a href="%s">help desk</a>.', 'pdc'), esc_url('https://www.advancedcustomfields.com/resources/upgrade-guide-pdc-pro/'), esc_url('https://www.advancedcustomfields.com/support/')); ?></p>
			</div>
		</div>		
	</div>
	
	<hr />
	
	<div class="feature-section">
		
		<h2><?php _e("New Features", 'pdc'); ?> 🎉</h2>
		
		<div class="pdc-three-col">
			
			<div>
				<h3><?php _e("Link Field", 'pdc'); ?></h3>
				<p><?php _e("The Link field provides a simple way to select or define a link (url, title, target).", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Group Field", 'pdc'); ?></h3>
				<p><?php _e("The Group field provides a simple way to create a group of fields.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("oEmbed Field", 'pdc'); ?></h3>
				<p><?php _e("The oEmbed field allows an easy way to embed videos, images, tweets, audio, and other content.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Clone Field", 'pdc'); ?> <span class="badge"><?php _e('Pro', 'pdc'); ?></span></h3>
				<p><?php _e("The clone field allows you to select and display existing fields.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("More AJAX", 'pdc'); ?></h3>
				<p><?php _e("More fields use AJAX powered search to speed up page loading.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Local JSON", 'pdc'); ?></h3>
				<p><?php _e("New auto export to JSON feature improves speed and allows for syncronisation.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Easy Import / Export", 'pdc'); ?></h3>
				<p><?php _e("Both import and export can easily be done through a new tools page.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("New Form Locations", 'pdc'); ?></h3>
				<p><?php _e("Fields can now be mapped to menus, menu items, comments, widgets and all user forms!", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("More Customization", 'pdc'); ?></h3>
				<p><?php _e("New PHP (and JS) actions and filters have been added to allow for more customization.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Fresh UI", 'pdc'); ?></h3>
				<p><?php _e("The entire plugin has had a design refresh including new field types, settings and design!", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("New Settings", 'pdc'); ?></h3>
				<p><?php _e("Field group settings have been added for Active, Label Placement, Instructions Placement and Description.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Better Front End Forms", 'pdc'); ?></h3>
				<p><?php _e("pdc_form() can now create a new post on submission with lots of new settings.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Better Validation", 'pdc'); ?></h3>
				<p><?php _e("Form validation is now done via PHP + AJAX in favour of only JS.", 'pdc'); ?></p>
			</div>
			
			<div>
				<h3><?php _e("Moving Fields", 'pdc'); ?></h3>
				<p><?php _e("New field group functionality allows you to move a field between groups & parents.", 'pdc'); ?></p>
			</div>
			
			<div><?php // intentional empty div for flex alignment ?></div>
			
		</div>
			
	</div>
		
<?php elseif( $active == 'changelog' ): ?>
	
	<p class="about-description"><?php printf(__("We think you'll love the changes in %s.", 'pdc'), $version); ?></p>
	
	<?php
	
	// extract changelog and parse markdown
	$readme = file_get_contents( pdc_get_path('readme.txt') );
	$changelog = '';
	if( preg_match( '/(= '.$version.' =)(.+?)(=|$)/s', $readme, $match ) && $match[2] ) {
		$changelog = pdc_parse_markdown( $match[2] );
	}
	echo pdc_parse_markdown($changelog);
	
endif; ?>
		
</div>