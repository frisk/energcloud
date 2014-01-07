<div class="container">
    <!-- Main hero unit for a primary marketing message or call to action -->
    <div class="cust-hero-unit">
      <h3>New Construction</h3>
      <?php 
        if(isset($success))
        {
          echo $success;
        }
        echo form_open(base_url('construction/create')); 
      ?>
        <div class="control-group">
          <label class="control-label" for="component_id">Construction Component:</label>
          <div class="controls">
            <select name="component_id">
              <?=$options ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <?php echo form_error('name'); ?>
          <label class="control-label" for="name">Construction Description:</label>
          <div class="controls">
            <textarea name="name"></textarea>
          </div>
        </div>
        <div class="control-group">
          <?php echo form_error('r_value'); ?>
          <label class="control-label" for="r_value">R-Value:</label>
          <div class="controls">
            <input type="text" name="r_value">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="units">Units:</label>
          <div class="controls">
            <select name="units">
              <option value="0">ft2-h-°F/Btu</option>
              <option value="1">m2-°C/W</option>
            </select>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <button type="submit" class="btn btn-success">Add</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div> <!-- /container -->