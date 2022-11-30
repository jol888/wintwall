import * as React from 'react';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import IconButton from '@mui/material/IconButton';
import IconAdd from '@mui/icons-material/Add';

function App() {
  return (
    <AppBar>
      <Toolbar>
        <Button>
          <Typography variant='h5'> Wintwall </Typography>
        </Button>
        <IconAdd/>
      </Toolbar>
    </AppBar>
  );
}

export default App;
