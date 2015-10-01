<div id="county-locator-body">
  <div id="county-office-location"></div>
  <div id="county-office-list-title">Find your county's office:</div>
  <select id="county-office-list" name="county-office-list">
    <option label="Select" value="none" selected="selected">Select</option>
    <?php 
      $transient = get_transient('county_office_locator');
      foreach($transient as &$value){
        $office = array(
          'county'=>str_replace(' County Office', '', $value['unitname']),
          'phone'=>$value['unitphonenumber'],
          'email'=>$value['unitemailaddress']
        );
        echo '<option label="' . $office['county'] . '" value="' . str_replace('"', '\'', json_encode($office)) . '">' . $office['county'] . "</option>\n";
      }
    ?>
  </select>
</div>
<script type="text/template" id="county-info">
  <a href="mailto:<%= email %>" id="contact-button" class="button round" data-dropdown="contact-drop" data-options="is_hover:true">Contact <%= county %> County</a><br />
  <div id="county-office-phone">
    <a class="county-office-phone" href="tel:<%= phone %>"><%= phone %></a>
  </div>
  <!-- Smaller, italic -->
</script>