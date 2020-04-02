<!-- Main view file above -->

<!-- HELP! -->

<?php if (isset($help_file) && file_exists($help_file)): ?>
<hr/>
<h2>Help</h2>
<div class="box no-print">
<?php include $help_file; ?>
</div>
<?php endif; ?>

<!-- END OF HELP -->

	</div>


    <!-- ##### Footer ##### -->

    <div id="footer">
      <div class="left doNotPrint">
        <a href="#Top">Top</a>
      </div>

      <div class="right doNotPrint">
        Modified: 12 July 2018 |
        Page Design: <a href="mailto:paulf@quillandmouse.com">Paul M. Foster</a>
      </div>

      <br class="doNotDisplay doNotPrint" />
    </div>

    <div class="subFooter doNotPrint">
        Copyright &copy; <?php echo date('Y'); ?> Paul M. Foster<br />
      <span class="doNotPrint">
        <a href="./index.php">Home</a>
      </span>
    </div>
  </body>
</html>
