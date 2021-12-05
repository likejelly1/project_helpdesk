        </section>
      </div>
    </div>
  </div>
  
  <?php echo view('normal_modal'); ?>
  <?php echo view('big_modal'); ?>
  <?php echo view("navigation_modal")?>
</body>

<footer>
  <!-- General JS Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="<?= site_url("public/stisla/js/stisla.js"); ?>"></script>

  <!-- JS Libraies -->

  <!-- Template JS File -->
  <script src="<?= site_url("public/stisla/js/scripts.js"); ?>"></script>
  <script src="<?= site_url("public/stisla/js/custom.js"); ?>"></script>
  <!-- Page Specific JS File -->

  <script type="text/javascript">
    $(document).ready(function() {
      $('#burger').on('click', function() {
            $('table').css("width", "100%");
      });

      // $('#ServiceModalButton').on('click',function () {
      //   event.preventDefault();
      //   $('#_ServiceModal').modal('show');
      // })
    
    });
  </script>
</footer>
</html>