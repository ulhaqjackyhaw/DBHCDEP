import React, { useEffect, useState } from 'react';
import { Pie } from 'react-chartjs-2';
import axios from 'axios';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale);

const EmployeeChart = () => {
  const [chartData, setChartData] = useState({
    labels: ['Organic', 'Non-Organic'],
    datasets: [{
      data: [0, 0], // Data dummy, akan diganti dengan data sebenarnya
      backgroundColor: ['#FF6384', '#36A2EB'],
      hoverBackgroundColor: ['#FF6384', '#36A2EB'],
    }],
  });

  useEffect(() => {
    // Mengambil data karyawan dari API
    axios.get('http://127.0.0.1:8000/api/employees') // Sesuaikan URL API Anda
      .then(response => {
        // Menghitung jumlah karyawan berdasarkan status (organic/non-organic)
        const organicCount = response.data.filter(employee => employee.STATUS_KEPEGAWAIAN === 'Organic').length;
        const nonOrganicCount = response.data.filter(employee => employee.STATUS_KEPEGAWAIAN !== 'Organic').length;

        // Memperbarui data untuk grafik pie chart
        setChartData({
          labels: ['Organic', 'Non-Organic'],
          datasets: [{
            data: [organicCount, nonOrganicCount],
            backgroundColor: ['#FF6384', '#36A2EB'],
            hoverBackgroundColor: ['#FF6384', '#36A2EB'],
          }],
        });
      })
      .catch(error => {
        console.error('There was an error fetching the data!', error);
      });
  }, []);

  return (
    <div className="container mt-4">
      <h2 className="text-center">Jumlah Karyawan Berdasarkan Status</h2>
      <div className="chart-container">
        <Pie data={chartData} />
      </div>
    </div>
  );
};

export default EmployeeChart;
