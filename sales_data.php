<canvas id="salesChart"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch("sales_data.php")
  .then(res => res.json())
  .then(data => {
      console.log(data);

      // Example: Weekly chart
      new Chart(document.getElementById("salesChart"), {
          type: "bar",
          data: {
              labels: Object.keys(data.weekly),
              datasets: [{
                  label: "Weekly Sales",
                  data: Object.values(data.weekly),
                  backgroundColor: "rgba(255, 64, 129, 0.6)"
              }]
          }
      });
  });
</script>
