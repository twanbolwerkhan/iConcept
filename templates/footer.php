

<? if ($current_page == 'login' || $current_page == 'register') {
  // include 'templates/logo_nav.php';
} else {
  include 'templates/footer_links.php';
}?>
<!--/.Footer-->

    <!-- SCRIPTS -->
    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="js/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="js/mdb.min.js"></script>
    <!-- Classie.js -->
    <script type="text/javascript" src="js/classie.js"></script>
    <!-- Rubrieken overlay JavaScript -->
    <script type="text/javascript" src="js/category_overlay.js"></script>
    <script type="text/javascript" src="js/search_overlay.js"></script>
    <script type="text/javascript">

    $('.carousel[data-type="multi"] .item').each(function() {
	var next = $(this).next();
	if (!next.length) {
		next = $(this).siblings(':first');
	}
	next.children(':first-child').clone().appendTo($(this));


  if (next.next().length>0) {
  next.next().children(':first-child').clone().appendTo($(this));
}
else {
  $(this).siblings(':first').children(':first-child').clone().appendTo($(this));
}
});
    </script>
    </script>



</body>

</html>
