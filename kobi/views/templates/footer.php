<footer class="container-fluid">
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 clearfix text-center col-centered">
			<p class="text-center"><?php echo '@'.date('Y'); ?></p>
		</div>
	</div>
</footer>
	<?php 
	
		// Include all Javascript files
		echo (isset($included_files) ? $this->html_utility->str_js($included_files) : '');
		
		// Run short Javascript scripts
		echo (isset($scripts) ? $this->html_utility->str_js_scripts($scripts) : '');
	?>
</body>
</html>