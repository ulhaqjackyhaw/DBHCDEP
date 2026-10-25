import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import EmployeeList from './components/EmployeeList';
import EmployeeChart from './components/EmployeeChart';
import './index.css';  // Style utama

// Rendering React components
ReactDOM.render(
  <Router>
    <Routes>
      <Route path="/" element={<EmployeeList />} />
      <Route path="/chart" element={<EmployeeChart />} />
    </Routes>
  </Router>,
  document.getElementById('root')
);
