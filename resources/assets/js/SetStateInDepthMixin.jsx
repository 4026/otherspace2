var SetStateInDepthMixin = {
    setStateInDepth: function(updatePath) {
        this.setState(React.addons.update(this.state, updatePath));
    }
};