/**
 * React element that governs the UI for the composition of messages.
 */

var MessageComposer = React.createClass({
    mixins: [SetStateInDepthMixin],
    getInitialState: function () {
        return {
            editing : false,
            message: {
                clause_1: {
                    type: null,
                    word_list: null,
                    word: null
                },
                conjunction: null,
                clause_2: {
                    type: null,
                    word_list: null,
                    word: null
                }
            }
        };
    },
    render: function () {

        if (!this.state.editing) {
            return (
                <div className="text-center">
                    <button type="button" className="btn btn-primary " onClick={this.beginEditing}>
                        <i className="fa fa-pencil-square-o" aria-hidden="true" />
                        Etch a message here
                    </button>
                </div>
            );
        }

        //Determine what options are currently displayed to the player.
        var current_options = [];
        var can_finish = false;
        if (this.state.message.clause_1.type == null) {
            current_options = window.message_grammar.clauses;
        } else if (this.state.message.clause_1.word_list == null) {
            current_options = Object.getOwnPropertyNames(window.message_grammar.words);
        } else if (this.state.message.clause_1.word == null) {
            current_options = window.message_grammar.words[this.state.message.clause_1.word_list];
        } else if (this.state.message.conjunction == null) {
            current_options = window.message_grammar.conjunctions;
            can_finish = true;
        } else if (this.state.message.clause_2.type == null) {
            current_options = window.message_grammar.clauses;
        } else if (this.state.message.clause_2.word_list == null) {
            current_options = Object.getOwnPropertyNames(window.message_grammar.words);
        } else if (this.state.message.clause_2.word == null) {
            current_options = window.message_grammar.words[this.state.message.clause_2.word_list];
        } else {
            can_finish = true;
        }

        //Build options list
        var options_list = [];
        for (var i = 0; i < current_options.length; ++i) {
            options_list.push(
                <button className="list-group-item" key={i} onClick={this.optionClicked.bind(this, i)}>
                    {current_options[i]}
                </button>
            );
        }

        var message_text = getMessageText(this.state.message);
        if (message_text == '') {
            message_text = "Select a message format:";
        }

        return (
            <div>
                <div className="well text-warning">
                    { message_text }
                </div>

                <div className="list-group" style={{maxHeight: 220, 'overflow-y': 'auto'}}>
                    {options_list}
                </div>

                <button className="btn btn-success pull-right" disabled={!can_finish} onClick={this.finishClicked}>
                    <i className="fa fa-pencil-square-o" />
                    Done
                </button>

                <button type="button" className="btn btn-default" onClick={this.backClicked}>
                    Back
                </button>
            </div>
        );
    },
    beginEditing : function() {
        this.setStateInDepth({editing : {$set : true}});
    },
    optionClicked: function(option_index) {
        var selected_list;

        if (this.state.message.clause_1.type == null) {
            this.setStateInDepth({message : {clause_1 : {type : {$set : option_index}}}});
        } else if (this.state.message.clause_1.word_list == null) {
            selected_list = Object.getOwnPropertyNames(window.message_grammar.words)[option_index];
            this.setStateInDepth({message : {clause_1 : {word_list : {$set : selected_list}}}});
        } else if (this.state.message.clause_1.word == null) {
            this.setStateInDepth({message : {clause_1 : {word : {$set : option_index}}}});
        } else if (this.state.message.conjunction == null) {
            this.setStateInDepth({message : {conjunction: {$set : option_index}}});
        } else if (this.state.message.clause_2.type == null) {
            this.setStateInDepth({message : {clause_2 : {type : {$set : option_index}}}});
        } else if (this.state.message.clause_2.word_list == null) {
            selected_list = Object.getOwnPropertyNames(window.message_grammar.words)[option_index];
            this.setStateInDepth({message : {clause_2 : {word_list : {$set : selected_list}}}});
        } else if (this.state.message.clause_2.word == null) {
            this.setStateInDepth({message : {clause_2 : {word : {$set : option_index}}}});
        }
    },
    backClicked: function() {
        if (this.state.message.clause_2.word != null) {
            this.setStateInDepth({message : {clause_2 : {word : {$set : null}}}});
        } else if (this.state.message.clause_2.word_list != null) {
            this.setStateInDepth({message : {clause_2 : {word_list : {$set : null}}}});
        } else if (this.state.message.clause_2.type != null) {
            this.setStateInDepth({message : {clause_2 : {type : {$set : null}}}});
        } else if (this.state.message.conjunction != null) {
            this.setStateInDepth({message : {conjunction: {$set : null}}});
        } else if (this.state.message.clause_1.word != null) {
            this.setStateInDepth({message : {clause_1 : {word : {$set : null}}}});
        } else if (this.state.message.clause_1.word_list != null) {
            this.setStateInDepth({message : {clause_1 : {word_list : {$set : null}}}});
        } else if (this.state.message.clause_1.type != null) {
            this.setStateInDepth({message : {clause_1 : {type : {$set : null}}}});
        } else {
            this.setStateInDepth({editing : {$set : false}});
        }
    },
    finishClicked: function() {
        var parameters = {
            latitude: PlayerLocation.instance().latitude,
            longitude: PlayerLocation.instance().longitude,
            message: this.state.message
        };
        $.post('/message', parameters)
            .done(function() {
                this.setState(this.getInitialState());
                updateLocation();
            }.bind(this));

    }
});

ReactDOM.render(
    <MessageComposer />,
    document.getElementById('react_messageComposer')
);
