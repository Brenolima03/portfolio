const express = require('express');
const path = require('path');
const app = express();
const PORT = process.env.PORT || 8080;

// Serve static files from both src and node_modules directories
app.use(express.static(path.join(__dirname, 'src')));
app.use('/node_modules', express.static(path.join(__dirname, 'node_modules')));

// Route all requests to the index.html file
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'src/index.html'));
});

// Start the server
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
  console.log(`Access your application at http://localhost:${PORT}`);
});