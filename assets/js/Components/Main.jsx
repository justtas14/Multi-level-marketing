import React, { Component } from 'react';
import { connect } from 'react-redux';
import { nameSearch, levelSearch, emailSearch, 
    phoneSearch, dateSearch, addCurrentPagination,
     scrollDown, addModal, closeModal, loadData } from '../store/actions/widget';
import Associate from './Item/Associate';
import SearchBar from './SearchBar/SearchBar';
import Modal from './Modal/Modal';
import { findAll, findBy } from '../services/AssociateSearchService';
import PageBar from './PageBar/PageBar';
import './Main.scss';

class Main extends Component {
    constructor(props) {
        super(props);
        this.changePage = this.changePage.bind(this);
        this.handleNameSearch = this.handleNameSearch.bind(this);
        this.handleEmailSearch = this.handleEmailSearch.bind(this);
    }

    componentDidMount () {
        findAll().then(response => {
            this.props.onLoadData(response);
        });
    }

    handleNameSearch(text) {
        let name = this.props.nameSearch;
        name = text.substr(0, 20);
        this.props.onNameSearch(name);
    }


    handleEmailSearch(text) {
        let email = this.props.emailSearch;
        email = text.substr(0, 20);
        this.props.onEmailSearch(email);
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

    changePage(page){
        const params = {
            page,
            nameField: this.props.nameSearch,
            emailField: this.props.emailSearch,
        };
        findBy(params).then(response => {
            this.props.onLoadData(response);
        });
    }

    showModal(id) {
        const modal = '/admin/associates/' + id;
        this.props.onAddModal(modal);
    }

    closeModal() {
        this.props.onCloseModal();
    }

    render() {
        const { associates, pages, currentPage } = this.props;
        const associateRows = associates.map((associate, index) => {
            return <Associate
                        key={associate.id}
                        id={associate.id}
                        name={associate.fullName}
                        level={associate.level}
                        email={associate.email}
                        phone={associate.mobilePhone}
                        date={associate.joinDate}
                        index={index}
                        firstNewAssociate={this.props.firstNewAssociate}
                        showModal={this.showModal.bind(this)}
                    />;
        });
        console.log(this.props.modal);
        return (
            <div className="main-searchContainer">
                <SearchBar
                    handleNameSearchInput={this.handleNameSearch}
                    handleEmailSearchInput={this.handleEmailSearch}
                />
                <div className="main-associatesContainer">
                    {associateRows}

                </div>
                <PageBar pages={pages} currentPage={currentPage} onClick={this.changePage} />

                <Modal
                    modal={this.props.modal}
                    showModal={this.props.showModal}
                    modalClosed={this.closeModal}
                />
            </div>
        );
    }
}

const mapStateToProps = state => {
    return {
        associates: state.widget.associates,
        nameSearch: state.widget.nameSearch,
        emailSearch: state.widget.emailSearch,
        pages: state.widget.pages,
        currentPage: state.widget.currentPage,
        scrollDown: state.widget.scrollDown,
        modal: state.widget.modal,
        showModal: state.widget.showModal,
    };
};

const mapDispatchToProps = dispatch => {
    return {
        onNameSearch: (name) => dispatch(nameSearch(name)),
        onEmailSearch: (email) => dispatch(emailSearch(email)),
        onScrollDown: (position) => dispatch(scrollDown(position)),
        onAddModal: (modal) => dispatch(addModal(modal)),
        onCloseModal: () => dispatch(closeModal()),
        onLoadData: (data) => dispatch(loadData(data)),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(Main);