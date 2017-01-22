<?php
	
	function createGraph($id, $taker, $maxScore, $maxTotal, $score){
		$scoreArr = range(1, $maxScore);
		$totalArr = range(0, $maxTotal);
		echo '<div id="user-'.$id.'" class="highchart-div"></div>';
		
		?>
		<script type="text/javascript">
		$(function () {
			$('#user-' + <?php echo json_encode($id); ?>).highcharts({
				title: {
					text: 'Student Performance Graph',
					x: -20 //center
				},
				subtitle: {
					text: <?php echo json_encode($taker); ?>,
					x: -20
				},
				xAxis: {
					
					title: {
						text: 'Test Number'
					},
					categories: <?php echo json_encode($scoreArr); ?>
				},
				yAxis: {
					title: {
						text: "Students's Score"
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}],
					categories: <?php echo json_encode($totalArr); ?>
				},
				
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle',
					borderWidth: 0
				},
				series: [{
					name: 'Score',
					data: <?php echo json_encode($score); ?>
				}]
			});
		});
		</script>
<?php
		
	}
?>