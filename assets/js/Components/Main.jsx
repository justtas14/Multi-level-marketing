import React, { Component } from 'react';
import { connect } from 'react-redux';
import { nameSearch, levelSearch, emailSearch, 
    phoneSearch, dateSearch, addCurrentPagination,
     scrollDown, addModal, closeModal, loadData } from '../store/actions/widget';
import Associate from './Item/Associate';
import SearchBar from './SearchBar/SearchBar';
import Modal from './Modal/Modal';
import axios from 'axios';

import './Main.scss';

class Main extends Component {
    constructor(props) {
        super(props);
        this.state = {
            scrolldown:'',
        };
    }

    componentDidMount () {
        axios.get('/admin/api/associates/1')
        .then(response => {
            //const associates = response.data.slice(0, 10);
            this.props.onLoadData(response.data);
            console.log(response.data);
        });  
    }

    handleNameSearch(text) {
        let name = this.props.nameSearch
        name = text.substr(0, 20)
        this.props.onNameSearch(name);
    }

    handleLevelSearch(text) {
        let level = this.props.levelSearch
        level = text.substr(0, 20)
        this.props.onLevelSearch(level);
    }

    handleEmailSearch(text) {
        let email = this.props.emailSearch
        email = text.substr(0, 20)
        this.props.onEmailSearch(email);
    }

    handlePhoneSearch(text) {
        let phone = this.props.phoneSearch
        phone = text.substr(0, 20)
        this.props.onPhoneSearch(phone);
    }

    handleDateSearch(text) {
        let date= this.props.dateSearch
        date = text.substr(0, 20)
        this.props.onDateSearch(date);
    }

    handleLoadAssociates() {
        let people = this.props.people;
        const indexOfLastAssociate = (this.props.currentPagination + 1) * this.props.paginationIndex;
        const currentAssociates = people.slice(0, indexOfLastAssociate);

        let loadCount = this.props.currentPagination;
        loadCount = loadCount + 1;
        this.props.onAddCurrentPagination(loadCount, currentAssociates);
            this.scrollToBottom();
    }

    showModal(id, name, level, email, phone, date, photo) {
        let modalData;
        axios.get('https://jsonplaceholder.typicode.com/posts/' + id)
            .then(response => {
                modalData = response.data;
            });
        let modal = {
            name,
            level,
            email,
            phone,
            date,
            photo,
        }
        this.props.onAddModal(modal);
    }

    closeModal() {
        this.props.onCloseModal();
    }

    scrollToBottom() {  
       this.state.scrolldown.scrollIntoView({ behavior: 'smooth' });
    }

    render() {

    let people = this.props.people;

    const indexOfLastAssociate = this.props.currentPagination * this.props.paginationIndex;
    const currentAssociates = people.slice(0, indexOfLastAssociate);

    let paginationButton;

    if (this.props.people.length > currentAssociates.length) {
        paginationButton = <div className="main-paginationButton" 
        onClick={()=>this.handleLoadAssociates()}>
            Load More Associates
            </div>
    }

        let filteredAssociates;
        if (this.props.currentPagination > 1) 
        filteredAssociates = this.props.currentAssociates.filter(
            (person) => {
                if (this.props.nameSearch !=='') {
                  return  person.name.toLowerCase().indexOf(this.props.nameSearch.toLowerCase()) !== -1;
                }
                if (this.props.levelSearch !=='') {
                    return  person.invitedBy.toLowerCase().indexOf(this.props.levelSearch.toLowerCase()) !== -1;
                }
                if (this.props.emailSearch !=='') {
                    return  person.email.toLowerCase().indexOf(this.props.emailSearch.toLowerCase()) !== -1;
                }
                if (this.props.phoneSearch !=='') {
                    return  person.tel.toLowerCase().indexOf(this.props.phoneSearch.toLowerCase()) !== -1;
                }
                else {
                    return  person.enrolmentDate.toLowerCase().indexOf(this.props.dateSearch.toLowerCase()) !== -1;
            }
        }
        );
        if (this.props.currentPagination === 1) {
            filteredAssociates = currentAssociates.filter(
                (person) => {
                    if (this.props.nameSearch !=='') {
                      return  person.name.toLowerCase().indexOf(this.props.nameSearch.toLowerCase()) !== -1;
                    }
                    if (this.props.levelSearch !=='') {
                        return  person.invitedBy.toLowerCase().indexOf(this.props.levelSearch.toLowerCase()) !== -1;
                    }
                    if (this.props.emailSearch !=='') {
                        return  person.email.toLowerCase().indexOf(this.props.emailSearch.toLowerCase()) !== -1;
                    }
                    if (this.props.phoneSearch !=='') {
                        return  person.tel.toLowerCase().indexOf(this.props.phoneSearch.toLowerCase()) !== -1;
                    }
                    else {
                        return  person.enrolmentDate.toLowerCase().indexOf(this.props.dateSearch.toLowerCase()) !== -1;
                }
            }
            );  
        }
    
        let associates;
            associates = filteredAssociates.map((associates, index) => {
                return <Associate 
                                        key={associates.id}
                                        id={associates.id}
                                        name={associates.name}
                                        level={associates.invitedBy}
                                        email={associates.email}
                                        phone={associates.tel}
                                        date={associates.enrolmentDate}
                                        index={index}
                                        firstNewAssociate={this.props.firstNewAssociate}
                                        showModal={this.showModal.bind(this)}
                                        />;
        });
        return (
            <div className="main-searchContainer">
                <SearchBar
                handleNameSearchInput={this.handleNameSearch}
                handleLevelSearchInput={this.handleLevelSearch}
                handleEmailSearchInput={this.handleEmailSearch}
                handlePhoneSearchInput={this.handlePhoneSearch}
                handleDateSearchInput={this.handleDateSearch}
                />
                <div className="main-associatesContainer">
                    {associates}
                    <div className="main-scrollDown" style={{ float:"left", clear: "both" }}
                        ref={(el) => { this.state.scrolldown = el; }}>
                    </div>
                </div>
                {paginationButton}
                <Modal modal={this.props.modal}
                        showModal={this.props.showModal}
                        modalClosed={this.closeModal}/>
            </div>
            );
        }
    }

const mapStateToProps = state => {
    return {
        people: state.widget.people,
        associates: state.widget.associates,
        currentAssociates: state.widget.currentAssociates,
        firstNewAssociate: state.widget.firstNewAssociate,
        nameSearch: state.widget.nameSearch,
        levelSearch: state.widget.levelSearch,
        emailSearch: state.widget.emailSearch,
        phoneSearch: state.widget.phoneSearch,
        dateSearch: state.widget.dateSearch,
        paginationIndex: state.widget.paginationIndex,
        currentPagination: state.widget.currentPagination,
        scrollDown: state.widget.scrollDown,
        modal: state.widget.modal,
        showModal: state.widget.showModal,
    };
};

const mapDispatchToProps = dispatch => {
    return {
        onNameSearch: (name) => dispatch(nameSearch(name)),
        onLevelSearch: (level) => dispatch(levelSearch(level)),
        onEmailSearch: (email) => dispatch(emailSearch(email)),
        onPhoneSearch: (phone) => dispatch(phoneSearch(phone)),
        onDateSearch: (date) => dispatch(dateSearch(date)),
        onAddCurrentPagination: (count, currentAssociates) => dispatch(addCurrentPagination(count, currentAssociates)),
        onScrollDown: (position) => dispatch(scrollDown(position)),
        onAddModal: (modal) => dispatch(addModal(modal)),
        onCloseModal: () => dispatch(closeModal()),
        onLoadData: (associates) => dispatch(loadData(associates)),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(Main);
