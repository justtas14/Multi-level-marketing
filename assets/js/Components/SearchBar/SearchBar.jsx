import React, { Component } from 'react';
import './SearchBar.scss';
import TextField from '@material-ui/core/TextField';

class SearchBar extends Component {
    constructor(props) {
        super(props);
        this.state = {
        };
    }

    render() { 
        return <div>
        
        <div className="searchBar-container">
             <div className="searchBar-inputContainer">
             <TextField
          id="filled-search"
          label="Name"
          onChange={(event) => this.props.handleNameSearchInput(event.target.value)}
          value={this.props.nameSearch}
          type="search"
          className=""
          margin="normal"    
        />
          </div>
          <div className="searchBar-inputLevelContainer">

          </div>
          <div className="searchBar-inputEmailContainer">
          <TextField
          id="filled-search"
          label="E-mail"
          onChange={(event) => this.props.handleEmailSearchInput(event.target.value)}
          value={this.props.emailSearch}
          type="search"
          className=""
          margin="normal"    
        />
          </div>
          <div className="searchBar-inputPhoneContainer">
          <TextField
          id="filled-search"
          label="Telephone"
          onChange={(event) => this.props.handlePhoneSearchInput(event.target.value)}
          value={this.props.phoneSearch}
          type="search"
          className=""
          margin="normal"    
        />
          </div>
          <div className="searchBar-inputDateContainer">
      
          </div>
             </div>
             <div className="searchBar-item">Name</div>
             <div className="searchBar-item">Total level one associates</div>
             <div className="searchBar-item">E-mail</div>
             <div className="searchBar-item">Telephone Number</div>
             <div className="searchBar-item">Date Of Enrolment</div>
             </div>
    }
}

export default SearchBar;
