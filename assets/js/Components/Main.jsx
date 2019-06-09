import React, { Component } from 'react';
import { connect } from 'react-redux';
import Modal from 'react-responsive-modal';
import { nameSearch, levelSearch, emailSearch, 
    phoneSearch, dateSearch, addCurrentPagination,
     scrollDown, addModal, closeModal, loadData, openModal } from '../store/actions/widget';
import Associate from './Item/Associate';
import SearchBar from './SearchBar/SearchBar';
import { findAll, findBy } from '../services/AssociateSearchService';
import PageBar from './PageBar/PageBar';
import './Main.scss';

function debounce(func, wait, immediate) {
    let timeout;
    return function() {
        let context = this, args = arguments;
        let later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        let callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

class Main extends Component {
    constructor(props) {
        super(props);
        this.changePage = this.changePage.bind(this);
        this.handleNameSearch = this.handleNameSearch.bind(this);
        this.handleEmailSearch = this.handleEmailSearch.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handleSearchDebounced = debounce(this.handleSearch, 2000);
    }

    componentDidMount () {
        findAll().then(response => {
            this.props.onLoadData(response);
        });
    }

    handleSearch() {
        const params = {
            nameField: this.props.nameSearch,
            emailField: this.props.emailSearch,
        };
        findBy(params).then(response => {
            this.props.onLoadData(response);
        });
    };

    handleNameSearch(text) {
        this.props.onNameSearch(text);
        this.handleSearchDebounced();
    }


    handleEmailSearch(text) {
        this.props.onEmailSearch(text);
        this.handleSearchDebounced();
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
        this.props.onOpenModal(id);
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
                <Modal onClose={this.props.onCloseModal} open={this.props.modalOpen}>
                    <iframe src={`/associate/info/${this.props.modalId}`} style={{width: "60vw", height: "50vw", border: '0px'}}/>
                </Modal>
            </div>
        );
    }
}

const mapStateToProps = state => ({
        associates: state.widget.associates,
        nameSearch: state.widget.nameSearch,
        emailSearch: state.widget.emailSearch,
        pages: state.widget.pages,
        currentPage: state.widget.currentPage,
        scrollDown: state.widget.scrollDown,
        modal: state.widget.modal,
        showModal: state.widget.showModal,
        modalOpen: state.widget.modalOpen,
        modalId: state.widget.modalId
});

const mapDispatchToProps = dispatch => {
    return {
        onNameSearch: (name) => dispatch(nameSearch(name)),
        onEmailSearch: (email) => dispatch(emailSearch(email)),
        onScrollDown: (position) => dispatch(scrollDown(position)),
        onAddModal: (modal) => dispatch(addModal(modal)),
        onCloseModal: () => dispatch(closeModal()),
        onOpenModal: (id) => dispatch(openModal(id)),
        onLoadData: (data) => dispatch(loadData(data)),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(Main);